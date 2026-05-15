<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'title' => 'تيشيرت قطن كلاسيكي',
                'description' => 'تيشيرت قطن 100% مريح ومثالي للاستخدام اليومي، متوفر بألوان متعددة',
                'sub_category_id' => 15, // تيشيرت قطن
                'images' => [
                    ['name' => 'أبيض', 'url' => 'images/products/product1.jpg'],
                    ['name' => 'أسود', 'url' => 'images/products/product2.jpg'],
                ],
                'sizes' => [2, 3, 4, 5], // S, M, L, XL
                'price' => 80,
                'discount_price' => null,
                'brand_id' => 1,
            ],
            [
                'title' => 'بنطلون جينز سليم فيت',
                'description' => 'بنطلون جينز عصري بقصة سليم فيت مريحة، خامة عالية الجودة',
                'sub_category_id' => 1, // جينز رجالي
                'images' => [
                    ['name' => 'أزرق', 'url' => 'images/products/product3.jpg'],
                    ['name' => 'أسود', 'url' => 'images/products/product4.jpg'],
                ],
                'sizes' => [2, 3, 4, 5, 6], // S, M, L, XL, XXL
                'price' => 120,
                'discount_price' => 99,
                'brand_id' => 2,
            ],
            [
                'title' => 'جاكيت جلد أسود',
                'description' => 'جاكيت جلد صناعي فاخر بتصميم عصري، مثالي للإطلالات الأنيقة',
                'sub_category_id' => 7, // جاكيت جلد
                'images' => [
                    ['name' => 'أسود', 'url' => 'images/products/product5.jpeg'],
                ],
                'sizes' => [3, 4, 5], // M, L, XL
                'price' => 250,
                'discount_price' => 199,
                'brand_id' => 3,
            ],
            [
                'title' => 'ترنج رياضي قطن',
                'description' => 'ترنج رياضي مصنوع من القطن الناعم، مريح ومناسب للرياضة والاستخدام اليومي',
                'sub_category_id' => 11, // ترنج رياضي
                'images' => [
                    ['name' => 'رمادي', 'url' => 'images/products/product6.jpeg'],
                    ['name' => 'كحلي', 'url' => 'images/products/product7.jpg'],
                ],
                'sizes' => [2, 3, 4, 5], // S, M, L, XL
                'price' => 150,
                'discount_price' => null,
                'brand_id' => 1,
            ],
            [
                'title' => 'قميص كاجوال كتان',
                'description' => 'قميص كتان خفيف مناسب للإطلالات الكاجوال، مريح في جميع الأوقات',
                'sub_category_id' => 28, // قميص كتان
                'images' => [
                    ['name' => 'بيج', 'url' => 'images/products/product8.jpeg'],
                    ['name' => 'أبيض', 'url' => 'images/products/product9.jpeg'],
                ],
                'sizes' => [3, 4, 5, 6], // M, L, XL, XXL
                'price' => 110,
                'discount_price' => 89,
                'brand_id' => 4,
            ],
            [
                'title' => 'كاب رياضي أديداس',
                'description' => 'كاب رياضي خفيف الوزن مع شبك خلفي قابل للتعديل، مناسب للرياضة والتنزه',
                'sub_category_id' => 34, // كاب رياضي
                'images' => [
                    ['name' => 'أسود', 'url' => 'images/products/product10.jpg'],
                ],
                'sizes' => [1], // Free Size
                'price' => 45,
                'discount_price' => null,
                'brand_id' => 2,
            ],
            [
                'title' => 'بنطلون كارغو كاجوال',
                'description' => 'بنطلون كارغو عصري بجيوب جانبية واسعة، مريح ومناسب للإطلالات اليومية',
                'sub_category_id' => 5, // بنطلون كارغو
                'images' => [
                    ['name' => 'زيتي', 'url' => 'images/products/product1.jpg'],
                    ['name' => 'بيج', 'url' => 'images/products/product2.jpg'],
                ],
                'sizes' => [2, 3, 4, 5], // S, M, L, XL
                'price' => 130,
                'discount_price' => 109,
                'brand_id' => 3,
            ],
            [
                'title' => 'حذاء رياضي خفيف',
                'description' => 'حذاء رياضي خفيف الوزن مناسب للجري والرياضة، نعل مريح ومتين',
                'sub_category_id' => 20, // حذاء رياضي
                'images' => [
                    ['name' => 'أبيض', 'url' => 'images/products/product3.jpg'],
                    ['name' => 'أسود', 'url' => 'images/products/product4.jpg'],
                ],
                'sizes' => [3, 4, 5, 6], // M, L, XL, XXL
                'price' => 180,
                'discount_price' => 149,
                'brand_id' => 5,
            ],
            [
                'title' => 'بولو شيرت قطن بيكيه',
                'description' => 'بولو شيرت من قماش البيكيه عالي الجودة، أنيق ومريح لجميع المناسبات',
                'sub_category_id' => 16, // بولو
                'images' => [
                    ['name' => 'أبيض', 'url' => 'images/products/product5.jpeg'],
                    ['name' => 'كحلي', 'url' => 'images/products/product6.jpeg'],
                ],
                'sizes' => [2, 3, 4, 5], // S, M, L, XL
                'price' => 95,
                'discount_price' => null,
                'brand_id' => 1,
            ],
            [
                'title' => 'بدلة رسمية كاملة',
                'description' => 'بدلة رسمية فاخرة من قماش عالي الجودة، مثالية للمناسبات والأعمال',
                'sub_category_id' => 30, // بدلة كاملة
                'images' => [
                    ['name' => 'أسود', 'url' => 'images/products/product7.jpg'],
                    ['name' => 'رمادي', 'url' => 'images/products/product8.jpeg'],
                ],
                'sizes' => [3, 4, 5, 6], // M, L, XL, XXL
                'price' => 450,
                'discount_price' => 399,
                'brand_id' => 4,
            ],
            [
                'title' => 'تلبيقة كاجوال شبابية',
                'description' => 'تلبيقة كاملة تتضمن تيشيرت وبنطلون كارغو وكاب، إطلالة شبابية عصرية',
                'sub_category_id' => 40, // تلبيقة كاجوال
                'images' => [
                    ['name' => 'بيج', 'url' => 'images/products/product1.jpg'],
                    ['name' => 'أسود', 'url' => 'images/products/product2.jpg'],
                ],
                'sizes' => [2, 3, 4, 5], // S, M, L, XL
                'price' => 220,
                'discount_price' => 189,
                'brand_id' => 1,
            ],
            [
                'title' => 'تلبيقة رسمية فاخرة',
                'description' => 'تلبيقة رسمية متكاملة تتضمن قميص وبنطلون وجاكيت، مثالية للمناسبات',
                'sub_category_id' => 41, // تلبيقة رسمية
                'images' => [
                    ['name' => 'أسود', 'url' => 'images/products/product3.jpg'],
                    ['name' => 'كحلي', 'url' => 'images/products/product4.jpg'],
                ],
                'sizes' => [3, 4, 5, 6], // M, L, XL, XXL
                'price' => 380,
                'discount_price' => 320,
                'brand_id' => 3,
            ],

        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
