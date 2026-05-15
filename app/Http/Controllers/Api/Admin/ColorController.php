<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    // جلب جميع الألوان
    public function index()
    {
        $colors = Color::orderBy('id')->get();

        return response()->json([
            'status' => true,
            'data' => ['colors' => $colors]
        ]);
    }

    // تحديث لون واحد
    public function update(Request $request, Color $color)
    {
        $request->validate([
            'value' => 'required|string|max:7', // hex color
        ], [
            'value.required' => 'قيمة اللون مطلوبة',
            'value.max' => 'قيمة اللون يجب أن تكون hex color صحيح',
        ]);

        $color->update([
            'value' => $request->value
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث اللون بنجاح',
            'data' => ['color' => $color]
        ]);
    }

    // تحديث جميع الألوان دفعة واحدة
    public function updateAll(Request $request)
    {
        $request->validate([
            'colors' => 'required|array',
            'colors.*.id' => 'required|exists:colors,id',
            'colors.*.value' => 'required|string|max:7',
        ]);

        foreach ($request->colors as $colorData) {
            Color::where('id', $colorData['id'])->update([
                'value' => $colorData['value']
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث الألوان بنجاح'
        ]);
    }

    // إعادة تعيين الألوان للقيم الافتراضية
    public function reset()
    {
        $defaultColors = [
            'color-primary' => '#1a1a2e',
            'color-secondary' => '#ffffff',
            'color-accent' => '#d4af37',
            'color-text' => '#2c3e50',
            'color-text-light' => '#7f8c8d',
            'color-background' => '#ffffff',
            'color-background-light' => '#f8f9fa',
        ];

        foreach ($defaultColors as $name => $value) {
            Color::where('name', $name)->update(['value' => $value]);
        }

        return response()->json([
            'status' => true,
            'message' => 'تم إعادة تعيين الألوان للقيم الافتراضية'
        ]);
    }
}
