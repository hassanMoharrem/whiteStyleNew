<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = [
            [
                'name' => 'color-primary',
                'value' => '#ffffff',
                'label' => 'اللون الأساسي',
            ],
            [
                'name' => 'color-secondary',
                'value' => '#bd2233',
                'label' => 'اللون الثانوي',
            ],
            [
                'name' => 'color-accent',
                'value' => '#2b2b2b',
                'label' => 'لون التمييز',
            ],
            [
                'name' => 'color-text',
                'value' => '#ffffff',
                'label' => 'لون النص الأساسي',
            ],
            [
                'name' => 'color-text-light',
                'value' => '#7f8c8d',
                'label' => 'لون النص الفاتح',
            ],
            [
                'name' => 'color-background',
                'value' => '#ffffff',
                'label' => 'لون الخلفية',
            ],
            [
                'name' => 'color-background-light',
                'value' => '#f8f9fa',
                'label' => 'لون الخلفية الفاتحة',
            ],
        ];

        foreach ($colors as $color) {
            DB::table('colors')->updateOrInsert(
                ['name' => $color['name']],
                [
                    'value' => $color['value'],
                    'label' => $color['label'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
