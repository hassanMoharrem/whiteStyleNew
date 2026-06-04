<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\SabeqService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of orders with pagination and search
     */
    public function index(Request $request)
    {
        $query = Order::with('user');

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
     * Store a newly created order in storage (for testing purposes, as orders are usually created from the site)
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'city_name' => 'required|string|max:255',
            'area_name' => 'required|string|max:255',
            'street_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'subtotal' => 'required|numeric|min:0'
        ]);

        $deliveryPrice = 0; // Default delivery price
        $total = $request->subtotal + $deliveryPrice;

        $order = Order::create([
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'city_name' => $request->city_name,
            'area_name' => $request->area_name,
            'street_name' => $request->street_name,
            'address' => $request->address,
            'description' => $request->description,
            'items' => $request->items,
            'subtotal' => $request->subtotal,
            'delivery_price' => $deliveryPrice,
            'total' => $total,
            'status' => 'pending'
        ]);
        $area_id = $request->area_id;
        $street_id = $request->street_id;

        // هنا يمكنك إضافة منطق لإنشاء شحنة في Sabeq وربط رقم التتبع بالطلب إذا لزم الأمر
        $sabeq = new SabeqService();
        $sabeqResponse = $sabeq->createParcel($order,$area_id, $street_id);
        if ($sabeqResponse['success']) {
            $order->update(
                ['track_number' => $sabeqResponse['track_number'],
                //  'delivery_price' => $sabeqResponse['delivery_price'] 
                ]
                );
        }

        return response()->json([
            'status' => true,
            'data' => ['order' => $order],
            'message' => 'تم إنشاء الطلب بنجاح'
        ], 201);
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
        if ($request->status === 'cancelled' && $order->track_number) {
            // إذا تم إلغاء الطلب وكان لديه رقم تتبع، قم بإلغاء الشحنة في Sabeq
            try {
                $sabeq = new SabeqService();
                $sabeq->cancelParcel($order->track_number);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'خطأ في إلغاء الطلب من سابق ولاحق'
                ], 500);
            }
           
        }

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
