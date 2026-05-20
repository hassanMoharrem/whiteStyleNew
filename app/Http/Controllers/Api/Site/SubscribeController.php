<?php

namespace App\Http\Controllers\Api\Site;

use App\Http\Controllers\Controller;
use App\Models\Subscribe;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SubscribeController extends Controller
{
    /**
     * Store a new Subscribe message
     */
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
                'message' => 'تم إضافة الاشتراك بنجاح!'
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'فشل التحقق من البيانات',
                'errors' => $e->errors()
            ], 422);
        }
    }
}
