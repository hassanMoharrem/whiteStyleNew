<?php

namespace App\Http\Controllers\Api\Site;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Get all categories with pagination for site
     */
    public function index(Request $request)
    {
        $query = Category::with(['subCategories:id,category_id,title'])->where('visible', true);

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
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
            case 'name':
                $query->orderBy('title', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        // Pagination
        $limit = $request->get('limit', 12);
        $categories = $query->paginate($limit);

        return response()->json([
            'status' => true,
            'data' => [
                'categories' => $categories->items(),
                'pagination' => [
                    'current_page' => $categories->currentPage(),
                    'last_page' => $categories->lastPage(),
                    'per_page' => $categories->perPage(),
                    'total' => $categories->total(),
                ]
            ],
            'message' => 'Categories retrieved successfully'
        ]);
    }

    /**
     * Get single category details
     */
    public function show($id)
    {
        $category = Category::with(['subCategories:id,category_id,title'])
            ->find($id);

        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => ['category' => $category],
            'message' => 'Category retrieved successfully'
        ]);
    }
}
