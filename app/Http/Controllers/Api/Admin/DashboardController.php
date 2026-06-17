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
        $totalNetAmount = Order::where('status', 'completed')->sum('total'); // Sum of all total (net amounts)
        $totalDeliveryPrice = Order::where('status', 'completed')->sum('delivery_price'); // Sum of all delivery prices
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
