<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Faq::query();

        if ($request->has('question')) {
            $query->where('question', 'like', '%' . $request->question . '%');
        }

        $faqs = $query->latest()->paginate(10);

        return response()->json([
            'status' => true,
            'data' => ['faqs' => $faqs],
            'message' => 'FAQs retrieved successfully'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required|string|max:1000',
            'answer' => 'required|string',
            'visible' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        $faq = Faq::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'visible' => $request->visible ?? true,
        ]);

        return response()->json([
            'status' => true,
            'data' => ['faq' => $faq],
            'message' => 'FAQ created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $faq = Faq::findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => ['faq' => $faq],
            'message' => 'FAQ retrieved successfully'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $faq = Faq::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'question' => 'required|string|max:1000',
            'answer' => 'required|string',
            'visible' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        $faq->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'visible' => $request->visible ?? $faq->visible,
        ]);

        return response()->json([
            'status' => true,
            'data' => ['faq' => $faq],
            'message' => 'FAQ updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $faq = Faq::findOrFail($id);
        $faq->delete();

        return response()->json([
            'status' => true,
            'message' => 'FAQ deleted successfully'
        ]);
    }
}
