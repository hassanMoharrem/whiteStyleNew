<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscribe;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SubscribeController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => ['subscribes' => Subscribe::orderBy('id', 'desc')->paginate(10)],
            'message' => 'Subscribes retrieved successfully'
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|string|max:255|unique:Subscribes,email',
            ]);

            $Subscribe = Subscribe::create($validated);

            return response()->json([
                'status' => true,
                'data' => ['Subscribe' => $Subscribe],
                'message' => 'Subscribe created successfully'
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function show(Subscribe $Subscribe)
    {
        return response()->json([
            'status' => true,
            'data' => ['Subscribe' => $Subscribe],
            'message' => 'Subscribe retrieved successfully'
        ]);
    }

    public function update(Request $request, Subscribe $Subscribe)
    {
        try {
            $validated = $request->validate([
                'email' => 'sometimes|string|max:255|unique:Subscribes,email,' . $Subscribe->id,
            ]);

            $Subscribe->update($validated);

            return response()->json([
                'status' => true,
                'data' => ['Subscribe' => $Subscribe],
                'message' => 'Subscribe updated successfully'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function destroy(Subscribe $Subscribe)
    {
        $Subscribe->delete();

        return response()->json([
            'status' => true,
            'data' => null,
            'message' => 'Subscribe deleted successfully'
        ]);
    }
}
