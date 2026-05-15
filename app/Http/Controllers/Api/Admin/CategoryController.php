<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => ['categories' => Category::orderBy('id', 'desc')->paginate(10)],
            'message' => 'Categories retrieved successfully'
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable',
                'visible' => 'boolean',
            ]);

            if (isset($validated['image'])) {
                $validated['image'] = $this->handleImageUpload($request->image, 'categories');
            }

            $category = Category::create($validated);

            return response()->json([
                'status' => true,
                'data' => ['category' => $category],
                'message' => 'Category created successfully'
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function show(Category $category)
    {
        $category->load('subCategories');

        return response()->json([
            'status' => true,
            'data' => ['category' => $category],
            'message' => 'Category retrieved successfully'
        ]);
    }

    public function update(Request $request, Category $category)
    {
        try {
            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable',
                'visible' => 'boolean',
            ]);

            if (isset($validated['image'])) {
                $oldImage = $category->image;
                $validated['image'] = $this->handleImageUpload($request->image, 'categories', $oldImage);
            }

            $category->update($validated);

            return response()->json([
                'status' => true,
                'data' => ['category' => $category],
                'message' => 'Category updated successfully'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function destroy(Category $category)
    {
        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return response()->json([
            'status' => true,
            'data' => null,
            'message' => 'Category deleted successfully'
        ]);
    }

    public function toggleVisible(Category $category)
    {
        $category->update(['visible' => !$category->visible]);

        return response()->json([
            'status' => true,
            'data' => ['category' => $category->fresh()],
            'message' => 'Category visibility toggled successfully'
        ]);
    }

    private function handleImageUpload($image, $folder, $oldImage = null)
    {
        if ($image instanceof \Illuminate\Http\UploadedFile) {
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }

            return $image->store($folder, 'public');
        }

        return $image;
    }
}
