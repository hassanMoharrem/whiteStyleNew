<?php

namespace App\Http\Controllers\Api\Site;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Get products for site with filters
     */
    public function index(Request $request)
    {
        $query = Product::with(['subCategory:id,title', 'brand:id,name']);

        // Filter by brand
        if ($request->has('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Filter by subcategory
        if ($request->has('sub_category_id')) {
            $query->where('sub_category_id', $request->sub_category_id);
        }

        // has_discount
        if ($request->has('has_discount')) {
            $hasDiscount = filter_var($request->has_discount, FILTER_VALIDATE_BOOLEAN);
            if ($hasDiscount) {
                $query->whereNotNull('discount_price')->whereColumn('discount_price', '<', 'price');
            } else {
                $query->whereNull('discount_price')->orWhereColumn('discount_price', '>=', 'price');
            }
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
        $limit = $request->get('limit', 10);
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

    /**
     * Get single product details
     */
    public function show($id)
    {
        $product = Product::with([
                'subCategory:id,title',
                'brand:id,name'
            ])
            ->find($id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => ['product' => $product],
            'message' => 'Product retrieved successfully'
        ]);
    }
}
