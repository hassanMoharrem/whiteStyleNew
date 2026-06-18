<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => ['products' => Product::with('subCategory', 'brand')->orderBy('id', 'desc')->paginate(10)],
            'message' => 'Products retrieved successfully'
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'discount_price' => 'nullable|numeric|min:0|lt:price',
                'review_count' => 'required|integer|between:1,5',
                'brand_id' => 'nullable|exists:brands,id',
                'sub_category_id' => 'required|exists:sub_categories,id',
                'images' => 'required|array',
                'images.*.name' => 'required|string',
                'images.*.url' => 'required',
                'sizes' => 'required|array',
                'sizes.*' => 'required|exists:sizes,id',
            ]);

            $validated['images'] = $this->handleProductImages($request->images);

            $product = Product::create($validated);

            $product->load('subCategory', 'brand');

            return response()->json([
                'status' => true,
                'data' => ['product' => $product],
                'message' => 'Product created successfully'
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function show(Product $product)
    {
        $product->load('subCategory', 'brand');

        return response()->json([
            'status' => true,
            'data' => ['product' => $product],
            'message' => 'Product retrieved successfully'
        ]);
    }

    public function update(Request $request, Product $product)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'discount_price' => 'nullable|numeric|min:0|lt:price',
                'review_count' => 'required|integer|between:1,5',
                'brand_id' => 'nullable|exists:brands,id',
                'sub_category_id' => 'required|exists:sub_categories,id',
                'images' => 'required|array',
                'images.*.name' => 'required_with:images|string',
                'images.*.url' => 'required_with:images',
                'sizes' => 'required|array',
                'sizes.*' => 'required_with:sizes|exists:sizes,id',
            ]);

            if (isset($validated['images'])) {
                $validated['images'] = $this->handleProductImagesUpdate($product->images, $request->images);
            }

            $product->update($validated);
            $product->load('subCategory', 'brand');

            return response()->json([
                'status' => true,
                'data' => ['product' => $product],
                'message' => 'Product updated successfully'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function destroy(Product $product)
    {
        $this->deleteProductImages($product->images);

        $product->delete();

        return response()->json([
            'status' => true,
            'data' => null,
            'message' => 'Product deleted successfully'
        ]);
    }

private function handleProductImages($images)
{
    $processedImages = [];

    foreach ($images as $image) {
        $imageData = [
            'name' => $image['name'],
            'url' => $image['url']
        ];

        if ($image['url'] instanceof \Illuminate\Http\UploadedFile) {
            $uploadedFile = $image['url'];

            // ضغط الصورة قبل الحفظ
            $compressedImage = $this->compressImage($uploadedFile);

            // اسم وامتداد الملف
            $extension = $uploadedFile->getClientOriginalExtension();
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $path = 'products/' . $filename;

            // حفظ الصورة المضغوطة بمسار storage/app/public/products
            Storage::disk('public')->put($path, $compressedImage);

            $imageData['url'] = $path;
        }

        $processedImages[] = $imageData;
    }

    return $processedImages;
}

private function handleProductImagesUpdate($oldImages, $newImages)
{
    $processedImages = [];
    $oldImagePaths = [];

    // Collect old image paths
    if ($oldImages && is_array($oldImages)) {
        foreach ($oldImages as $oldImage) {
            if (isset($oldImage['url'])) {
                $oldImagePaths[] = $oldImage['url'];
            }
        }
    }

    foreach ($newImages as $image) {
        $imageData = [
            'name' => $image['name'],
            'url' => $image['url']
        ];

        // If it's a new uploaded file
        if ($image['url'] instanceof \Illuminate\Http\UploadedFile) {
            $uploadedFile = $image['url'];

            // ضغط الصورة قبل الحفظ
            $compressedImage = $this->compressImage($uploadedFile);

            $extension = $uploadedFile->getClientOriginalExtension();
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $path = 'products/' . $filename;

            Storage::disk('public')->put($path, $compressedImage);

            $imageData['url'] = $path;
        }
        // If it's an existing path (string), keep it and remove from deletion list
        elseif (is_string($image['url'])) {
            // Remove this path from old images so it won't be deleted
            $oldImagePaths = array_diff($oldImagePaths, [$image['url']]);
        }

        $processedImages[] = $imageData;
    }

    // Delete only the images that were removed (not in new images list)
    foreach ($oldImagePaths as $pathToDelete) {
        if (Storage::disk('public')->exists($pathToDelete)) {
            Storage::disk('public')->delete($pathToDelete);
        }
    }

    return $processedImages;
}

    private function deleteProductImages($images)
    {
        if (!$images || !is_array($images)) {
            return;
        }

        foreach ($images as $image) {
            if (isset($image['url']) && Storage::disk('public')->exists($image['url'])) {
                Storage::disk('public')->delete($image['url']);
            }
        }
    }
    private function compressImage(\Illuminate\Http\UploadedFile $file): string
{
    $extension = strtolower($file->getClientOriginalExtension());
    $fullPath = $file->getRealPath();

    switch ($extension) {
        case 'jpg':
        case 'jpeg':
            $image = imagecreatefromjpeg($fullPath);
            ob_start();
            imagejpeg($image, null, 75); // جودة 75
            $data = ob_get_clean();
            break;

        case 'png':
            $image = imagecreatefrompng($fullPath);
            ob_start();
            imagepng($image, null, 7); // ضغط 0-9 (7 قوي وكويس)
            $data = ob_get_clean();
            break;

        case 'webp':
            $image = imagecreatefromwebp($fullPath);
            ob_start();
            imagewebp($image, null, 75);
            $data = ob_get_clean();
            break;

        default:
            // امتداد غير مدعوم (مثل gif) — رجّع الملف الأصلي بدون تغيير
            return file_get_contents($fullPath);
    }

    imagedestroy($image);

    return $data;
}
}
