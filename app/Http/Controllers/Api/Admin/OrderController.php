<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of orders with pagination and search
     */
    public function index(Request $request)
    {
        $query = Order::query();

        // Search by customer name, phone, or order ID
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $limit = $request->get('limit', 10);
        $orders = $query->paginate($limit);

        return response()->json([
            'status' => true,
            'data' => [
                'orders' => $orders
            ],
            'message' => 'Orders retrieved successfully'
        ]);
    }

    /**
     * Display the specified order
     */
    public function show($id)
    {
        $order = Order::query()->find($id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'الطلب غير موجود'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => ['order' => $order],
            'message' => 'Order retrieved successfully'
        ]);
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'الطلب غير موجود'
            ], 404);
        }

        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        $order->update([
            'status' => $request->status
        ]);

        return response()->json([
            'status' => true,
            'data' => ['order' => $order],
            'message' => 'تم تحديث حالة الطلب بنجاح'
        ]);
    }

    /**
     * Remove the specified order
     */
    public function destroy($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'الطلب غير موجود'
            ], 404);
        }

        $order->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم حذف الطلب بنجاح'
        ]);
    }

    /**
     * Print order invoice
     */
    public function print($id)
    {
        $order = Order::query()->find($id);

        if (!$order) {
            abort(404, 'الطلب غير موجود');
        }

        return view('orders.print', compact('order'));
    }
}
