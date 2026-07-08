<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\City;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getStatistics()
    {
        // إحصائيات عامة
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $processingOrders = Order::where('status', 'processing')->count();
        $completedOrders = Order::where('status', 'completed')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();

        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalCities = Order::select('city_name')->distinct()->count();
        $totalBrands = Brand::count();

        // المبيعات
        $totalSales = Order::where('status', 'completed')->sum('total');
        $totalDeliveryPrice = Order::where('status', 'completed')->sum('delivery_price');
        $totalNetAmount = $totalSales - $totalDeliveryPrice; // صافي بعد خصم التوصيل

        $todaySales = Order::where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('total');
        $monthSales = Order::where('status', 'completed')
            ->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->sum('total');

        // آخر 7 أيام - مبيعات
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $sales = Order::where('status', 'completed')
                ->whereDate('created_at', $date)
                ->sum('total');

            $last7Days[] = [
                'date' => now()->subDays($i)->format('d/m'),
                'sales' => (float) $sales
            ];
        }

        // آخر 6 أشهر - مبيعات
        $last6Months = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $sales = Order::where('status', 'completed')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('total');

            $last6Months[] = [
                'month' => $month->locale('ar')->format('M'),
                'sales' => (float) $sales
            ];
        }

        // أحدث الطلبات
        $recentOrders = Order::query()
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'customer_name' => $order->customer_name,
                    'total' => $order->total,
                    'status' => $order->status,
                    'created_at' => $order->created_at->format('Y-m-d H:i'),
                ];
            });

        // أفضل 5 مدن مبيعاً
        $topCities = Order::select('city_name', DB::raw('COUNT(*) as total_orders'))
            ->groupBy('city_name')
            ->orderByDesc('total_orders')
            ->take(5)
            ->get();

        // ================= تفاصيل الطلبات الملغاة (عام) =================
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();
        $daysElapsedInMonth = Carbon::now()->day;

        $cancelledToday = Order::where('status', 'cancelled')
            ->whereDate('updated_at', $today)->count();

        $cancelledThisWeek = Order::where('status', 'cancelled')
            ->where('updated_at', '>=', $startOfWeek)->count();

        $cancelledThisMonth = Order::where('status', 'cancelled')
            ->where('updated_at', '>=', $startOfMonth)->count();

        $cancelledValueTotal = Order::where('status', 'cancelled')->sum('total');
        $cancelledValueToday = Order::where('status', 'cancelled')
            ->whereDate('updated_at', $today)->sum('total');
        $cancelledValueThisMonth = Order::where('status', 'cancelled')
            ->where('updated_at', '>=', $startOfMonth)->sum('total');

        $cancellationRateOverall = $totalOrders > 0
            ? round(($cancelledOrders / $totalOrders) * 100, 2) : 0;

        $totalOrdersThisMonth = Order::where('created_at', '>=', $startOfMonth)->count();
        $cancellationRateThisMonth = $totalOrdersThisMonth > 0
            ? round(($cancelledThisMonth / $totalOrdersThisMonth) * 100, 2) : 0;

        $avgCancelledPerDay = $daysElapsedInMonth > 0
            ? round($cancelledThisMonth / $daysElapsedInMonth, 2) : 0;

        $cancelledLast7Days = Order::where('status', 'cancelled')
            ->where('updated_at', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(updated_at) as date, COUNT(*) as count, SUM(total) as value')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ================= الطلبات الملغاة حسب الفرع (المستخدم) =================
        // بيربط كل طلب ملغي بصاحبه (الفرع) عشان نعرف مين بيرجّعله طلبات أكتر
        $cancelledByBranch = Order::where('status', 'cancelled')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'users.id as user_id',
                'users.name as branch_name',
                DB::raw('COUNT(orders.id) as cancelled_count'),
                DB::raw('SUM(orders.total) as cancelled_value'),
                DB::raw('SUM(CASE WHEN DATE(orders.updated_at) = CURDATE() THEN 1 ELSE 0 END) as cancelled_today'),
                DB::raw('SUM(CASE WHEN orders.updated_at >= "' . $startOfMonth . '" THEN 1 ELSE 0 END) as cancelled_this_month')
            )
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('cancelled_count')
            ->get();

        // نسبة إلغاء كل فرع من إجمالي طلباته (يحتاج استعلام منفصل لإجمالي طلبات كل فرع)
        $totalOrdersByBranch = Order::join('users', 'orders.user_id', '=', 'users.id')
            ->select('users.id as user_id', DB::raw('COUNT(orders.id) as total_count'))
            ->groupBy('users.id')
            ->pluck('total_count', 'user_id');

        $cancelledByBranch = $cancelledByBranch->map(function ($branch) use ($totalOrdersByBranch) {
            $branchTotal = $totalOrdersByBranch[$branch->user_id] ?? 0;
            $branch->cancellation_rate = $branchTotal > 0
                ? round(($branch->cancelled_count / $branchTotal) * 100, 2) . '%'
                : '0%';
            $branch->cancelled_value = (float) $branch->cancelled_value;
            return $branch;
        });

        return response()->json([
            'status' => true,
            'data' => [
                'overview' => [
                    'total_orders' => $totalOrders,
                    'pending_orders' => $pendingOrders,
                    'processing_orders' => $processingOrders,
                    'completed_orders' => $completedOrders,
                    'cancelled_orders' => $cancelledOrders,
                    'total_products' => $totalProducts,
                    'total_categories' => $totalCategories,
                    'total_cities' => $totalCities,
                    'total_brands' => $totalBrands,
                    'total_sales' => (float) $totalSales,
                    'total_net_amount' => (float) $totalNetAmount,
                    'total_delivery_price' => (float) $totalDeliveryPrice,
                    'today_sales' => (float) $todaySales,
                    'month_sales' => (float) $monthSales,
                ],
                'cancelled_details' => [
                    'today' => $cancelledToday,
                    'this_week' => $cancelledThisWeek,
                    'this_month' => $cancelledThisMonth,
                    'value_total' => (float) $cancelledValueTotal,
                    'value_today' => (float) $cancelledValueToday,
                    'value_this_month' => (float) $cancelledValueThisMonth,
                    'cancellation_rate_overall' => $cancellationRateOverall . '%',
                    'cancellation_rate_this_month' => $cancellationRateThisMonth . '%',
                    'avg_cancelled_per_day_this_month' => $avgCancelledPerDay,
                    'last_7_days_breakdown' => $cancelledLast7Days,
                ],
                'cancelled_by_branch' => $cancelledByBranch,
                'charts' => [
                    'last_7_days' => $last7Days,
                    'last_6_months' => $last6Months,
                ],
                'recent_orders' => $recentOrders,
                'top_cities' => $topCities,
            ]
        ]);
    }
}