<?php

namespace Database\Seeders;

use App\Models\SubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subCategories = [
            // بناطيل (category_id: 1)
            ['title' => 'جينز رجالي - Men Jeans', 'category_id' => 1],
            ['title' => 'بنطلون كلاسيكي - Classic Pants', 'category_id' => 1],
            ['title' => 'بنطلون رياضي - Sport Pants', 'category_id' => 1],
            ['title' => 'شورت - Shorts', 'category_id' => 1],
            ['title' => 'بنطلون كارغو - Cargo Pants', 'category_id' => 1],

            // جكيتات (category_id: 2)
            ['title' => 'جاكيت جينز - Denim Jacket', 'category_id' => 2],
            ['title' => 'جاكيت جلد - Leather Jacket', 'category_id' => 2],
            ['title' => 'جاكيت رياضي - Sport Jacket', 'category_id' => 2],
            ['title' => 'جاكيت شتوي - Winter Jacket', 'category_id' => 2],
            ['title' => 'بليزر - Blazer', 'category_id' => 2],

            // ترنجات (category_id: 3)
            ['title' => 'ترنج رياضي - Sport Suit', 'category_id' => 3],
            ['title' => 'ترنج كاجوال - Casual Suit', 'category_id' => 3],
            ['title' => 'ترنج شتوي - Winter Suit', 'category_id' => 3],
            ['title' => 'ترنج صيفي - Summer Suit', 'category_id' => 3],

            // بلايز (category_id: 4)
            ['title' => 'تيشيرت قطن - Cotton T-Shirt', 'category_id' => 4],
            ['title' => 'بولو - Polo Shirt', 'category_id' => 4],
            ['title' => 'تيشيرت رياضي - Sport T-Shirt', 'category_id' => 4],
            ['title' => 'تيشيرت بطبعة - Printed T-Shirt', 'category_id' => 4],
            ['title' => 'سويت شيرت - Sweatshirt', 'category_id' => 4],

            // أحذية (category_id: 5)
            ['title' => 'حذاء رياضي - Sports Shoes', 'category_id' => 5],
            ['title' => 'حذاء كاجوال - Casual Shoes', 'category_id' => 5],
            ['title' => 'حذاء رسمي - Formal Shoes', 'category_id' => 5],
            ['title' => 'صندل - Sandals', 'category_id' => 5],
            ['title' => 'حذاء جري - Running Shoes', 'category_id' => 5],

            // قمصان (category_id: 6)
            ['title' => 'قميص رسمي - Formal Shirt', 'category_id' => 6],
            ['title' => 'قميص كاجوال - Casual Shirt', 'category_id' => 6],
            ['title' => 'قميص جينز - Denim Shirt', 'category_id' => 6],
            ['title' => 'قميص كتان - Linen Shirt', 'category_id' => 6],
            ['title' => 'قميص مربعات - Checkered Shirt', 'category_id' => 6],

            // أطقم رسمية (category_id: 7)
            ['title' => 'بدلة كاملة - Full Suit', 'category_id' => 7],
            ['title' => 'بدلة قطعتين - Two Piece Suit', 'category_id' => 7],
            ['title' => 'بدلة ثلاث قطع - Three Piece Suit', 'category_id' => 7],
            ['title' => 'فيست - Vest', 'category_id' => 7],

            // طواقي (category_id: 8)
            ['title' => 'كاب رياضي - Sport Cap', 'category_id' => 8],
            ['title' => 'كاب كلاسيكي - Classic Cap', 'category_id' => 8],
            ['title' => 'بيني - Beanie', 'category_id' => 8],
            ['title' => 'قبعة شمسية - Sun Hat', 'category_id' => 8],
            ['title' => 'باندانا - Bandana', 'category_id' => 8],

            // تلبيقاتنا (category_id: 9)
            ['title' => 'تلبيقة كاملة - Full Outfit', 'category_id' => 9],
            ['title' => 'تلبيقة كاجوال - Casual Outfit', 'category_id' => 9],
            ['title' => 'تلبيقة رسمية - Formal Outfit', 'category_id' => 9],
            ['title' => 'تلبيقة رياضية - Sport Outfit', 'category_id' => 9],
            ['title' => 'تلبيقة صيفية - Summer Outfit', 'category_id' => 9],
        ];

        foreach ($subCategories as $subCategory) {
            SubCategory::create($subCategory);
        }
    }
}
