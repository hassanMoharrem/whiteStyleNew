<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CityController extends Controller
{
    /**
     * Display a listing of cities with pagination
     */
    public function index(Request $request)
    {
        $query = City::query();

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $limit = $request->get('limit', 10);
        $cities = $query->paginate($limit);

        return response()->json([
            'status' => true,
            'data' => [
                'cities' => $cities
            ],
            'message' => 'Cities retrieved successfully'
        ]);
    }

    /**
     * Store a newly created city
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:cities,name',
            'delivery_price' => 'required|numeric|min:0',
        ], [
            'name.required' => 'اسم المدينة مطلوب',
            'name.unique' => 'المدينة موجودة مسبقاً',
            'delivery_price.required' => 'سعر التوصيل مطلوب',
            'delivery_price.numeric' => 'سعر التوصيل يجب أن يكون رقماً',
            'delivery_price.min' => 'سعر التوصيل لا يمكن أن يكون سالباً',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $city = City::create([
            'name' => $request->name,
            'delivery_price' => $request->delivery_price,
        ]);

        return response()->json([
            'status' => true,
            'data' => ['city' => $city],
            'message' => 'تم إضافة المدينة بنجاح'
        ], 201);
    }

    /**
     * Display the specified city
     */
    public function show($id)
    {
        $city = City::find($id);

        if (!$city) {
            return response()->json([
                'status' => false,
                'message' => 'المدينة غير موجودة'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => ['city' => $city],
            'message' => 'City retrieved successfully'
        ]);
    }

    /**
     * Update the specified city
     */
    public function update(Request $request, $id)
    {
        $city = City::find($id);

        if (!$city) {
            return response()->json([
                'status' => false,
                'message' => 'المدينة غير موجودة'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:cities,name,' . $id,
            'delivery_price' => 'required|numeric|min:0',
        ], [
            'name.required' => 'اسم المدينة مطلوب',
            'name.unique' => 'المدينة موجودة مسبقاً',
            'delivery_price.required' => 'سعر التوصيل مطلوب',
            'delivery_price.numeric' => 'سعر التوصيل يجب أن يكون رقماً',
            'delivery_price.min' => 'سعر التوصيل لا يمكن أن يكون سالباً',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $city->update([
            'name' => $request->name,
            'delivery_price' => $request->delivery_price,
        ]);

        return response()->json([
            'status' => true,
            'data' => ['city' => $city],
            'message' => 'تم تحديث المدينة بنجاح'
        ]);
    }

    /**
     * Remove the specified city
     */
    public function destroy($id)
    {
        $city = City::find($id);

        if (!$city) {
            return response()->json([
                'status' => false,
                'message' => 'المدينة غير موجودة'
            ], 404);
        }

        $city->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم حذف المدينة بنجاح'
        ]);
    }
}
