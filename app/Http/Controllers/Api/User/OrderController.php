<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\DeliveryPriceService;

class OrderController extends Controller
{
    /**
     * Display a listing of user's orders with pagination and search
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Order::where('user_id', $user->id);

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
            'message' => 'تم جلب الطلبات بنجاح'
        ]);
    }

    /**
     * Get user order statistics
     */
    public function stats(Request $request)
    {
        $user = $request->user();

        $stats = [
            'total_orders' => Order::where('user_id', $user->id)->count(),
            'total_value' => Order::where('user_id', $user->id)->where('status','completed')->sum('total'),
            'total_amount' => Order::where('user_id', $user->id)->where('status', 'completed')->sum('total'),
            'total_delivery_price' => Order::where('user_id', $user->id)->where('status', 'completed')->sum('delivery_price'),
            'pending' => Order::where('user_id', $user->id)->where('status', 'pending')->count(),
            'processing' => Order::where('user_id', $user->id)->where('status', 'processing')->count(),
            'completed' => Order::where('user_id', $user->id)->where('status', 'completed')->count(),
            'cancelled' => Order::where('user_id', $user->id)->where('status', 'cancelled')->count(),
        ];

        return response()->json([
            'status' => true,
            'data' => [
                'stats' => $stats
            ],
            'message' => 'تم جلب الإحصائيات بنجاح'
        ]);
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'city_id' => 'required|integer', // This is area_id from frontend
            'area_id' => 'nullable|integer', // Keep for backward compatibility
            'street_id' => 'nullable|integer',
            'city_name' => 'required|string|max:255',
            'area_name' => 'required|string|max:255',
            'street_name' => 'nullable|string|max:255',
            'address' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:1000',
            'items.*.price' => 'required|numeric|min:0',
            'subtotal' => 'nullable|numeric|min:0',
            'delivery_price' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'service_type' => 'required|in:تسليم وتحصيل,تبديل طرد,تسليم فقط,استلام طرد',
        ]);

        // Use city_id from request (which is actually area_id from frontend)
        $areaId = $request->city_id ?? $request->area_id;

        // Calculate subtotal from items
        $calculatedSubtotal = 0;
        foreach ($request->items as $item) {
            $calculatedSubtotal += $item['price'];
        }

        // Verify delivery price based on city_name
        $expectedDeliveryPrice = DeliveryPriceService::calculatePriceFromCityName($request->city_name);

        // Use the total sent from frontend (allows manual override)
        $total = $request->total;
        $subtotal = $request->subtotal ?? $calculatedSubtotal;
        $deliveryPrice = $request->delivery_price ?? $expectedDeliveryPrice;
        $order = Order::create([
            'user_id' => $user->id,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'city_name' => $request->city_name,
            'area_name' => $request->area_name,
            'street_name' => $request->street_name ?? '',
            'address' => $request->address,
            'description' => $request->notes ?? '',
            'items' => $request->items,
            'subtotal' => $subtotal,
            'delivery_price' => $deliveryPrice,
            'total' => $total,
            'status' => 'pending',
            'service_type' => $request->service_type
        ]);

        // Create parcel in Sabeq
        try {
            $sabeq = new \App\Services\SabeqService($user);
            $sabeqResponse = $sabeq->createParcelUser($order, $areaId, $request->street_id);

            if (isset($sabeqResponse['track_number'])) {
            $order->update([
                'track_number' => $sabeqResponse['track_number'],
                'status' => 'completed'
            ]);
            // $sabeq->markAsReady($sabeqResponse['track_number']);
        }
        } catch (\Exception $e) {
            Log::error('Sabeq parcel creation failed: ' . $e->getMessage());
            // Continue without failing the order creation
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
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $order = Order::where('user_id', $user->id)->find($id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'الطلب غير موجود'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => ['order' => $order],
            'message' => 'تم جلب الطلب بنجاح'
        ]);
    }

    /**
     * Update the specified order (only if status is pending)
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $order = Order::where('user_id', $user->id)->find($id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'الطلب غير موجود'
            ], 404);
        }

        // Only allow updates for pending orders
        if ($order->status !== 'pending') {
            return response()->json([
                'status' => false,
                'message' => 'لا يمكن تعديل الطلب إلا إذا كان في حالة "قيد الانتظار"'
            ], 403);
        }

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'city_name' => 'required|string|max:255',
            'area_name' => 'required|string|max:255',
            'street_name' => 'nullable|string|max:255',
            'address' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        $order->update([
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'city_name' => $request->city_name,
            'area_name' => $request->area_name,
            'street_name' => $request->street_name ?? '',
            'address' => $request->address,
            'description' => $request->notes ?? '',
        ]);

        return response()->json([
            'status' => true,
            'data' => ['order' => $order],
            'message' => 'تم تحديث الطلب بنجاح'
        ]);
    }

    /**
     * Cancel the specified order
     */
    public function cancel(Request $request, $id)
    {
        $user = $request->user();
        $order = Order::where('user_id', $user->id)->find($id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'الطلب غير موجود'
            ], 404);
        }

        // Only allow cancellation for pending or processing orders
        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json([
                'status' => false,
                'message' => 'لا يمكن إلغاء هذا الطلب'
            ], 403);
        }

        // Cancel on Sabeq if track_number exists
        if ($order->track_number) {
            try {
                $sabeq = new \App\Services\SabeqService($user);
                $sabeqResponse = $sabeq->cancelParcel($order->track_number);

                Log::info('Sabeq cancellation response: ' . json_encode($sabeqResponse));
            } catch (\Exception $e) {
                Log::error('Sabeq parcel cancellation failed: ' . $e->getMessage());
                // Continue with local cancellation even if Sabeq fails
            }
        }

        // Update order status
        $order->update(['status' => 'cancelled']);

        return response()->json([
            'status' => true,
            'data' => ['order' => $order],
            'message' => 'تم إلغاء الطلب بنجاح'
        ]);
    }    
    public function completed(Request $request, $id)
    {
        $user = $request->user();
        $order = Order::where('user_id', $user->id)->find($id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'الطلب غير موجود'
            ], 404);
        }

        // Only allow cancellation for pending or processing orders
        // if (!in_array($order->status, ['pending', 'processing'])) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'لا يمكن إلغاء هذا الطلب'
        //     ], 403);
        // }

        // Cancel on Sabeq if track_number exists
        // if ($order->track_number) {
        //     try {
        //         $sabeq = new \App\Services\SabeqService($user);
        //         $sabeqResponse = $sabeq->markAsReady($order->track_number);

        //         Log::info('Sabeq completed response: ' . json_encode($sabeqResponse));
        //     } catch (\Exception $e) {
                // Log::error('Sabeq parcel completed failed: ' . $e->getMessage());
                // Continue with local completed even if Sabeq fails
        //     }
        // }


        // Update order status
        $order->update(['status' => 'completed']);

        return response()->json([
            'status' => true,
            'data' => ['order' => $order],
            'message' => 'تم إتمام الطلب بنجاح'
        ]);
    }
    public function print(Request $request, $id)
{
    $user = $request->user();
    $order = Order::where('user_id', $user->id)->findOrFail($id);

    if (!$order->track_number) {
        return response()->json([
            'status' => false,
            'message' => 'لا يوجد رقم تتبع لهذا الطلب'
        ], 404);
    }

    $sabeq = new \App\Services\SabeqService($user);
    $html = $sabeq->printParcel($order->track_number);

    return response($html)->header('Content-Type', 'text/html');
}

    /**
     * Remove the specified order (only if status is pending)
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $order = Order::where('user_id', $user->id)->find($id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'الطلب غير موجود'
            ], 404);
        }

        // Only allow deletion for pending orders
        if ($order->status !== 'pending') {
            return response()->json([
                'status' => false,
                'message' => 'لا يمكن حذف الطلب إلا إذا كان في حالة "قيد الانتظار"'
            ], 403);
        }

        $order->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم حذف الطلب بنجاح'
        ]);
    }


    public function findByTrackNumber(Request $request)
{
    $user = $request->user();
    $trackNumber = $request->track_number;

    $order = Order::where('user_id', $user->id)
        ->where('track_number', $trackNumber)
        ->first();

    if (!$order) {
        return response()->json([
            'status' => false,
            'message' => 'لا يوجد طلب بهذا الرقم'
        ], 404);
    }

    return response()->json([
        'status' => true,
        'data' => ['order' => $order]
    ]);
}
public function bulkMarkReady(Request $request)
{
    $user = $request->user();
    $trackNumbers = $request->track_numbers; // array

    $sabeq = new \App\Services\SabeqService($user);
    $results = [];

    foreach ($trackNumbers as $trackNumber) {
        try {
            $sabeq->markAsReady($trackNumber);
            $results[$trackNumber] = 'success';
        } catch (\Exception $e) {
            $results[$trackNumber] = 'failed';
        }
    }

    return response()->json([
        'status' => true,
        'data' => ['results' => $results],
        'message' => 'تم تسليم الطلبات لشركة النقل'
    ]);
}
}
