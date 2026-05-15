<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'title' => 'بناطيل-Pants',
                'description' => 'أحدث صيحات الموضة في البناطيل',
                'image' => 'images/categories/pants.jpeg',
                'visible' => true,
            ],
            [
                'title' => 'جكيتات - Jackets',
                'description' => 'جكيتات أنيقة وعصرية',
                'image' => 'images/categories/jacket.jpeg',
                'visible' => true,
            ],
            [
                'title' => 'ترنجات - Suits',
                'description' => 'ترنجات مريحة وعصرية',
                'image' => 'images/categories/suits.jpeg',
                'visible' => true,
            ],
            [
                'title' => 'بلايز - T-Shirt',
                'description' => 'بلايز من أحدث الموديلات و أكثرها أناقه',
                'image' => 'images/categories/tops.jpeg',
                'visible' => true,
            ],
            [
                'title' => 'أحذية - Sneakers',
                'description' => 'أحذية وأدوات رياضية للأنشطة الخارجية',
                'image' => 'images/categories/shoes.jpeg',
                'visible' => true,
            ],
            [
                'title' => 'قمصان -Shirts',
                'description' => 'قمصان من أحدث صيحات الموضة',
                'image' => 'images/categories/shirt.jpeg',
                'visible' => true,
            ],
            [
                'title' => 'أطقم رسمية - Sets',
                'description' => 'أطقم رسمية من أحدث صيحات الموضة',
                'image' => 'images/categories/formal.png',
                'visible' => true,
            ],[
                'title' => 'طواقي - Caps',
                'description' => 'أطقم رسمية من أحدث صيحات الموضة',
                'image' => 'images/categories/formal.png',
                'visible' => true,
            ],[
                'title' => 'تلبيقاتنا - Our Fittings',
                'description' => 'تلبيقاتنا من أحدث صيحات الموضة',
                'image' => 'images/categories/fittings.jpeg',
                'visible' => true,
            ],

        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
