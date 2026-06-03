<?php

namespace App\Http\Controllers\Api\Site;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\SabeqService;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Create a new order
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'area_id' => 'required|integer',
            'street_id' => 'nullable|integer',
            'city_name' => 'required|string|max:255',
            'area_name' => 'required|string|max:255',
            'street_name' => 'nullable|string|max:255',
            'address' => 'required|string',
            'description' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.product_name' => 'required|string',
            'items.*.product_image' => 'nullable|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.size' => 'nullable|string',
            'subtotal' => 'required|numeric|min:0',
        ], [
            'customer_name.required' => 'الاسم مطلوب',
            'customer_phone.required' => 'رقم الهاتف مطلوب',
            'city_name.required' => 'المدينة مطلوبة',
            'area_name.required' => 'المنطقة مطلوبة',
            'street_name.required' => 'الشارع مطلوب',
            'address.required' => 'العنوان مطلوب',
            'items.required' => 'يجب إضافة منتجات للطلب',
            'items.min' => 'يجب إضافة منتج واحد على الأقل',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Get city to calculate delivery price
        // $city = City::where('name', $request->city_name)->first();
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
            'status' => 'pending',
        ]);
        // Create parcel in Sabeq
        try {
            $area_id = $request->area_id;
            $street_id = $request->street_id;

            Log::info('Attempting to create Sabeq parcel', [
                'order_id' => $order->id,
                'area_id' => $area_id,
                'street_id' => $street_id
            ]);

            $sabeq = new SabeqService();
            $sabeqResponse = $sabeq->createParcel($order, $area_id, $street_id);

            Log::info('Sabeq response received', ['response' => $sabeqResponse]);

            if (isset($sabeqResponse['track_number'])) {
                $order->update([
                    'track_number' => $sabeqResponse['track_number'],
                    'delivery_price' => $sabeqResponse['delivery_cost'],
                    'total' => $request->subtotal + $sabeqResponse['delivery_cost'],
                ]);
                Log::info('Order updated with Sabeq data', ['order_id' => $order->id, 'track_number' => $sabeqResponse['track_number']]);
            } else {
                Log::warning('Sabeq response missing track_number', ['response' => $sabeqResponse]);
            }
        } catch (\Exception $e) {
            Log::error('Sabeq parcel creation failed (Site)', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'order_id' => $order->id
            ]);
            // Continue without failing the order creation
        }

        return response()->json([
            'status' => true,
            'data' => ['order' => $order],
            'message' => 'تم إنشاء الطلب بنجاح'
        ], 201);
    }

    /**
     * Get order information     */
    public function informationParcel($orderId,$trackNumber)
    {
        $order = Order::where('id', $orderId)->where('track_number', $trackNumber)->first();
        if(!$order){
            return response()->json([
            'status' => false,
            'message' => 'معلومات الطلب ليست موجودة'
            ],404);
        }
        try {
            $sabeq = new SabeqService();
            $sabeqResponse = $sabeq->informationParcel($trackNumber);
        } catch (\Exception $e) {
            Log::error('Sabeq parcel information failed (Site): ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => ' خطأ في جلب معلومات الطلب من سابق ولاحق '
            ], 500);
        }
        return response()->json([
            'status' => true,
            'data' => ['parcel' => $sabeqResponse],
            'message' => 'تم جلب بيانات الطلب بنجاح'
        ]);
    }

    public function cancelParcel($orderId,$trackNumber)
    {
        $order = Order::where('id', $orderId)->where('track_number', $trackNumber)->first();
        if(!$order){
            return response()->json([
            'status' => false,
            'message' => 'معلومات الطلب ليست موجودة'
            ],404);
        }
        try {
        $sabeq = new SabeqService();
        $sabeqResponse = $sabeq->cancelParcel($trackNumber);
        } catch (\Exception $e) {
            Log::error('Sabeq parcel cancellation failed (Site): ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'خطأ في إلغاء الطلب من سابق ولاحق'
            ], 500);
        }
        $order->update([
            'status' => 'cancelled'
        ]);
        return response()->json([
            'status' => true,
            'data' => ['parcel' => $sabeqResponse],
            'message' => 'تم حذف الطلب بنجاح'
        ]);
    }

    /**
     * Get all cities for checkout
     */
    public function getCities()
    {
        $cities = City::orderBy('name', 'asc')->get(['id', 'name', 'delivery_price']);

        return response()->json([
            'status' => true,
            'data' => ['cities' => $cities],
            'message' => 'Cities retrieved successfully'
        ]);
    }
}
