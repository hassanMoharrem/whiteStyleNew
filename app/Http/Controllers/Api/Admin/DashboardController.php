<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * لوحة الأدمن — مفلترة بالفترة (الافتراضي: اليوم)
     * period: daily | weekly | monthly
     */
    public function getStatistics(Request $request)
    {
        // ================= فلتر الفترة (يحكم اللوحة كلها) =================
        $period = $request->get('period', 'daily');

        $periodStart = match ($period) {
            'weekly'  => Carbon::now()->subWeek(),
            'monthly' => Carbon::now()->startOfMonth(),
            default   => Carbon::today(), // daily
        };

        $periodQuery = fn() => Order::where('created_at', '>=', $periodStart);

        // ================= نظرة عامة (مفلترة بالفترة) =================
        $overviewRow = $periodQuery()
            ->selectRaw("
                COUNT(*) as orders_count,
                COALESCE(SUM(CASE WHEN status != 'cancelled' THEN total ELSE 0 END), 0) as sales_value,
                COALESCE(SUM(CASE WHEN status != 'cancelled' THEN subtotal ELSE 0 END), 0) as net_value,
                COALESCE(SUM(CASE WHEN status != 'cancelled' THEN delivery_price ELSE 0 END), 0) as delivery_fees,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_count,
                SUM(CASE WHEN delivery_status = 'returned' THEN 1 ELSE 0 END) as returned_count,
                SUM(CASE WHEN delivery_status IN ('warehouse','on_way','delivered','completed') THEN 1 ELSE 0 END) as with_carrier_count,
                SUM(CASE WHEN delivery_status IN ('created','packed_ready') THEN 1 ELSE 0 END) as at_branch_count,
                COALESCE(SUM(CASE WHEN delivery_status = 'completed' THEN subtotal ELSE 0 END), 0) as collected_value,
                SUM(CASE WHEN delivery_status = 'completed' THEN 1 ELSE 0 END) as collected_count
            ")
            ->first();

        $overview = [
            'orders_count'       => (int) $overviewRow->orders_count,
            'sales_value'        => (float) $overviewRow->sales_value,
            'net_value'          => (float) $overviewRow->net_value,
            'delivery_fees'      => (float) $overviewRow->delivery_fees,
            'cancelled_count'    => (int) $overviewRow->cancelled_count,
            'returned_count'     => (int) $overviewRow->returned_count,
            'with_carrier_count' => (int) $overviewRow->with_carrier_count,
            'at_branch_count'    => (int) $overviewRow->at_branch_count,
            'collected_value'    => (float) $overviewRow->collected_value,
            'collected_count'    => (int) $overviewRow->collected_count,
        ];

        // ================= أداء الفروع (مفلتر بالفترة — نجم اللوحة) =================
        $branches = Order::where('orders.created_at', '>=', $periodStart)
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'users.id as user_id',
                'users.name as branch_name',
                DB::raw('COUNT(orders.id) as orders_count'),
                DB::raw("COALESCE(SUM(CASE WHEN orders.status != 'cancelled' THEN orders.total ELSE 0 END), 0) as sales_value"),
                DB::raw("COALESCE(SUM(CASE WHEN orders.status != 'cancelled' THEN orders.subtotal ELSE 0 END), 0) as net_value"),
                DB::raw("SUM(CASE WHEN orders.delivery_status = 'returned' THEN 1 ELSE 0 END) as returned_count"),
                DB::raw("SUM(CASE WHEN orders.delivery_status IN ('warehouse','on_way','delivered','completed') THEN 1 ELSE 0 END) as with_carrier"),
                DB::raw("SUM(CASE WHEN orders.delivery_status IN ('created','packed_ready') THEN 1 ELSE 0 END) as at_branch"),
                DB::raw("SUM(CASE WHEN orders.delivery_status IN ('delivered','completed') THEN 1 ELSE 0 END) as delivered_count"),
                DB::raw("SUM(CASE WHEN orders.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_count")
            )
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('sales_value')
            ->get()
            ->map(function ($b) {
                $shippable = (int) $b->orders_count - (int) $b->cancelled_count;
                return [
                    'user_id'         => $b->user_id,
                    'branch_name'     => $b->branch_name,
                    'orders_count'    => (int) $b->orders_count,
                    'sales_value'     => (float) $b->sales_value,
                    'net_value'       => (float) $b->net_value,
                    'returned_count'  => (int) $b->returned_count,
                    'with_carrier'    => (int) $b->with_carrier,
                    'at_branch'       => (int) $b->at_branch,
                    'delivered_count' => (int) $b->delivered_count,
                    'cancelled_count' => (int) $b->cancelled_count,
                    // نسبة الاستلام: كم من الطرود القابلة للشحن استلمتها شركة النقل
                    'pickup_rate'     => $shippable > 0
                        ? round(((int) $b->with_carrier / $shippable) * 100)
                        : 0,
                ];
            });

        // ================= ملخص الفلوس العام (ثابت: اليوم / الأسبوع / الشهر) =================
        $moneyWindow = function (Carbon $start) {
            $row = Order::where('created_at', '>=', $start)
                ->where('status', '!=', 'cancelled')
                ->selectRaw('
                    COALESCE(SUM(total), 0) as sales,
                    COALESCE(SUM(subtotal), 0) as net,
                    COUNT(*) as orders
                ')
                ->first();

            return [
                'sales'  => (float) $row->sales,
                'net'    => (float) $row->net,
                'orders' => (int) $row->orders,
            ];
        };

        $moneySummary = [
            'today' => $moneyWindow(Carbon::today()),
            'week'  => $moneyWindow(Carbon::now()->startOfWeek()),
            'month' => $moneyWindow(Carbon::now()->startOfMonth()),
        ];

        // ================= مبيعات آخر 7 أيام (ثابتة — للرسم) =================
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $sales = Order::where('status', '!=', 'cancelled')
                ->whereDate('created_at', $date)
                ->sum('total');

            $last7Days[] = [
                'date'  => now()->subDays($i)->format('d/m'),
                'sales' => (float) $sales,
            ];
        }

        // ================= أحدث الطلبات (ضمن الفترة) =================
        $recentOrders = Order::with('user:id,name')
            ->where('created_at', '>=', $periodStart)
            ->latest()
            ->take(6)
            ->get()
            ->map(function ($order) {
                return [
                    'id'              => $order->id,
                    'branch_name'     => $order->user->name ?? '—',
                    'customer_name'   => $order->customer_name,
                    'total'           => (float) $order->total,
                    'status'          => $order->status,
                    'delivery_status' => $order->delivery_status,
                    'created_at'      => $order->created_at->format('H:i'),
                ];
            });

        return response()->json([
            'status' => true,
            'data' => [
                'period'        => $period,
                'overview'      => $overview,
                'branches'      => $branches,
                'money_summary' => $moneySummary,
                'charts'        => [
                    'last_7_days' => $last7Days,
                ],
                'recent_orders' => $recentOrders,
            ]
        ]);
    }
}