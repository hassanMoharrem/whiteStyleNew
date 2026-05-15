<?php

namespace Database\Seeders;

use App\Models\Slider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sliders = [
            [
                'title' => 'مجموعة الصيف 2026',
                'description' => 'احصل على خصم يصل إلى 50% على جميع الملابس الصيفية',
                'image' => 'images/sliders/slider1.jpg',
                'visible' => true,
            ],
            [
                'title' => 'وصل حديثاً',
                'description' => 'تصفح أحدث صيحات الموضة لدينا',
                'image' => 'images/sliders/slider2.jpg',
                'visible' => true,
            ],
            [
                'title' => 'تخفيضات الشتاء',
                'description' => 'ابقَ دافئاً مع مجموعتنا الشتوية - خصم يصل إلى 40%',
                'image' => 'images/sliders/slider3.png',
                'visible' => true,
            ]
        ];

        foreach ($sliders as $slider) {
            Slider::create($slider);
        }
    }
}
