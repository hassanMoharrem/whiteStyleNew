<?php

namespace App\Http\Controllers\Api\Site;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Get all brands
     */
    public function index()
    {
        $brands = Brand::orderBy('name', 'asc')->get();

        return response()->json([
            'status' => true,
            'data' => ['brands' => $brands],
            'message' => 'Brands retrieved successfully'
        ]);
    }

    /**
     * Get single brand with its products
     */
    public function show($id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json([
                'status' => false,
                'message' => 'Brand not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => ['brand' => $brand],
            'message' => 'Brand retrieved successfully'
        ]);
    }

    /**
     * Get products by brand
     */
    public function products(Request $request, $brandId)
    {
        $query = Product::where('brand_id', $brandId)
            ->with(['subCategory:id,title', 'brand:id,name']);

        // Filter by subcategory if provided
        if ($request->has('sub_category_id')) {
            $query->where('sub_category_id', $request->sub_category_id);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('title', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        // Pagination
        $limit = $request->get('limit', 12);
        $products = $query->paginate($limit);

        return response()->json([
            'status' => true,
            'data' => [
                'products' => $products->items(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ]
            ],
            'message' => 'Products retrieved successfully'
        ]);
    }
}
