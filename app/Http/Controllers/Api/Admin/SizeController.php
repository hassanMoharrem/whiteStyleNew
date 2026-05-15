<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SizeController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => ['sizes' => Size::orderBy('id', 'desc')->paginate(10)],
            'message' => 'Sizes retrieved successfully'
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:sizes,name',
            ]);

            $size = Size::create($validated);

            return response()->json([
                'status' => true,
                'data' => ['size' => $size],
                'message' => 'Size created successfully'
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function show(Size $size)
    {
        return response()->json([
            'status' => true,
            'data' => ['size' => $size],
            'message' => 'Size retrieved successfully'
        ]);
    }

    public function update(Request $request, Size $size)
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255|unique:sizes,name,' . $size->id,
            ]);

            $size->update($validated);

            return response()->json([
                'status' => true,
                'data' => ['size' => $size],
                'message' => 'Size updated successfully'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function destroy(Size $size)
    {
        $size->delete();

        return response()->json([
            'status' => true,
            'data' => null,
            'message' => 'Size deleted successfully'
        ]);
    }
}
