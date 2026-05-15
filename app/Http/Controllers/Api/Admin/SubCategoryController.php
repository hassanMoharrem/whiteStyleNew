<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        return response()->json([
            'status' => true,
            'data' => ['sub_categories' => SubCategory::with('category')->orderBy('id', 'desc')->paginate($perPage)],
            'message' => 'Sub-categories retrieved successfully'
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
            ]);

            $subCategory = SubCategory::create($validated);
            $subCategory->load('category');

            return response()->json([
                'status' => true,
                'data' => ['sub_category' => $subCategory],
                'message' => 'Sub-category created successfully'
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function show(SubCategory $subCategory)
    {
        $subCategory->load('category');

        return response()->json([
            'status' => true,
            'data' => ['sub_category' => $subCategory],
            'message' => 'Sub-category retrieved successfully'
        ]);
    }

    public function update(Request $request, SubCategory $subCategory)
    {
        try {
            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'category_id' => 'sometimes|exists:categories,id',
            ]);

            $subCategory->update($validated);
            $subCategory->load('category');

            return response()->json([
                'status' => true,
                'data' => ['sub_category' => $subCategory],
                'message' => 'Sub-category updated successfully'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function destroy(SubCategory $subCategory)
    {
        $subCategory->delete();

        return response()->json([
            'status' => true,
            'data' => null,
            'message' => 'Sub-category deleted successfully'
        ]);
    }
}
