<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        return response()->json([
            'status' => true,
            'data' => ['brands' => Brand::orderBy('id', 'desc')->paginate($perPage)],
            'message' => 'Brands retrieved successfully'
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'image' => 'nullable',
            ]);

            if (isset($validated['image'])) {
                $validated['image'] = $this->handleImageUpload($request->image, 'brands');
            }

            $brand = Brand::create($validated);

            return response()->json([
                'status' => true,
                'data' => ['brand' => $brand],
                'message' => 'Brand created successfully'
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function show(Brand $brand)
    {
        return response()->json([
            'status' => true,
            'data' => ['brand' => $brand],
            'message' => 'Brand retrieved successfully'
        ]);
    }

    public function update(Request $request, Brand $brand)
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'image' => 'nullable',
            ]);

            if (isset($validated['image'])) {
                $oldImage = $brand->image;
                $validated['image'] = $this->handleImageUpload($request->image, 'brands', $oldImage);
            }

            $brand->update($validated);

            return response()->json([
                'status' => true,
                'data' => ['brand' => $brand],
                'message' => 'Brand updated successfully'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function destroy(Brand $brand)
    {
        if ($brand->image && Storage::disk('public')->exists($brand->image)) {
            Storage::disk('public')->delete($brand->image);
        }

        $brand->delete();

        return response()->json([
            'status' => true,
            'data' => null,
            'message' => 'Brand deleted successfully'
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
