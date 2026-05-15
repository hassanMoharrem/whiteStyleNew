<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::orderBy('id', 'desc')->paginate(10);

        return response()->json([
            'status' => true,
            'data' => ['sliders' => $sliders],
            'message' => 'Sliders retrieved successfully'
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'image' => 'required',
                'visible' => 'boolean',
            ]);

            $validated['image'] = $this->handleImageUpload($request->image, 'sliders');

            $slider = Slider::create($validated);

            return response()->json([
                'status' => true,
                'data' => ['slider' => $slider],
                'message' => 'Slider created successfully'
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function show(Slider $slider)
    {
        return response()->json([
            'status' => true,
            'data' => ['slider' => $slider],
            'message' => 'Slider retrieved successfully'
        ]);
    }

    public function update(Request $request, Slider $slider)
    {
        try {
            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'image' => 'sometimes',
                'visible' => 'boolean',
            ]);

            if (isset($validated['image'])) {
                $oldImage = $slider->image;
                $validated['image'] = $this->handleImageUpload($request->image, 'sliders', $oldImage);
            }

            $slider->update($validated);

            return response()->json([
                'status' => true,
                'data' => ['slider' => $slider],
                'message' => 'Slider updated successfully'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function destroy(Slider $slider)
    {
        if ($slider->image && Storage::disk('public')->exists($slider->image)) {
            Storage::disk('public')->delete($slider->image);
        }

        $slider->delete();

        return response()->json([
            'status' => true,
            'data' => null,
            'message' => 'Slider deleted successfully'
        ]);
    }

    public function toggleVisible(Slider $slider)
    {
        $slider->update(['visible' => !$slider->visible]);

        return response()->json([
            'status' => true,
            'data' => ['slider' => $slider->fresh()],
            'message' => 'Slider visibility toggled successfully'
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
