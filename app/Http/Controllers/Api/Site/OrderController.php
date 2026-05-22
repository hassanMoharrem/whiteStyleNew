<?php

namespace App\Http\Controllers\Api\Site;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            'city_name' => 'required|string|max:255',
            'area_name' => 'required|string|max:255',
            'street_name' => 'required|string|max:255',
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
        $deliveryPrice = 25; // Default delivery price
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

        return response()->json([
            'status' => true,
            'data' => ['order' => $order],
            'message' => 'تم إنشاء الطلب بنجاح'
        ], 201);
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
