<?php

namespace App\Http\Controllers\Api\Site;

use App\Http\Controllers\Controller;
use App\Models\Color;

class ColorController extends Controller
{
    public function index()
    {
        $colors = Color::orderBy('id')->get(['name', 'value']);

        // تحويل إلى key-value pairs
        $colorPairs = [];
        foreach ($colors as $color) {
            $colorPairs[$color->name] = $color->value;
        }

        return response()->json([
            'status' => true,
            'data' => ['colors' => $colorPairs]
        ]);
    }
}
