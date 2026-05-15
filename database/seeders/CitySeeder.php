<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            // المدن الفلسطينية في الضفة الغربية والقدس
            ['name' => 'القدس', 'delivery_price' => 15.00],
            ['name' => 'رام الله', 'delivery_price' => 10.00],
            ['name' => 'البيرة', 'delivery_price' => 10.00],
            ['name' => 'الخليل', 'delivery_price' => 12.00],
            ['name' => 'بيت لحم', 'delivery_price' => 11.00],
            ['name' => 'نابلس', 'delivery_price' => 13.00],
            ['name' => 'جنين', 'delivery_price' => 14.00],
            ['name' => 'طولكرم', 'delivery_price' => 12.00],
            ['name' => 'قلقيلية', 'delivery_price' => 12.00],
            ['name' => 'سلفيت', 'delivery_price' => 11.00],
            ['name' => 'أريحا', 'delivery_price' => 13.00],
            ['name' => 'طوباس', 'delivery_price' => 14.00],
            ['name' => 'الأغوار الشمالية', 'delivery_price' => 15.00],

            // مدن أخرى في الضفة الغربية
            ['name' => 'بيت جالا', 'delivery_price' => 11.00],
            ['name' => 'بيت ساحور', 'delivery_price' => 11.00],
            ['name' => 'الدوحة', 'delivery_price' => 11.00],
            ['name' => 'يطا', 'delivery_price' => 13.00],
            ['name' => 'دورا', 'delivery_price' => 12.00],
            ['name' => 'حلحول', 'delivery_price' => 12.00],
            ['name' => 'بني نعيم', 'delivery_price' => 12.00],
            ['name' => 'السموع', 'delivery_price' => 13.00],
            ['name' => 'سعير', 'delivery_price' => 12.00],
            ['name' => 'ترقوميا', 'delivery_price' => 12.00],

            // منطقة رام الله
            ['name' => 'بيتونيا', 'delivery_price' => 10.00],
            ['name' => 'كفر عقب', 'delivery_price' => 10.00],
            ['name' => 'بيت إيبا', 'delivery_price' => 10.00],
            ['name' => 'عين يبرود', 'delivery_price' => 11.00],
            ['name' => 'سلواد', 'delivery_price' => 11.00],
            ['name' => 'دير دبوان', 'delivery_price' => 11.00],
            ['name' => 'ترمسعيا', 'delivery_price' => 11.00],

            // منطقة نابلس
            ['name' => 'طلوزة', 'delivery_price' => 13.00],
            ['name' => 'عصيرة الشمالية', 'delivery_price' => 13.00],
            ['name' => 'بلاطة', 'delivery_price' => 13.00],
            ['name' => 'حوارة', 'delivery_price' => 13.00],
            ['name' => 'عقربا', 'delivery_price' => 14.00],
            ['name' => 'بيت فوريك', 'delivery_price' => 13.00],

            // منطقة جنين
            ['name' => 'قباطية', 'delivery_price' => 14.00],
            ['name' => 'عرابة', 'delivery_price' => 14.00],
            ['name' => 'يعبد', 'delivery_price' => 14.00],
            ['name' => 'برقين', 'delivery_price' => 14.00],
            ['name' => 'سيلة الحارثية', 'delivery_price' => 14.00],

            // منطقة طولكرم
            ['name' => 'عنبتا', 'delivery_price' => 12.00],
            ['name' => 'ذنابة', 'delivery_price' => 12.00],
            ['name' => 'بلعا', 'delivery_price' => 12.00],
            ['name' => 'قفين', 'delivery_price' => 12.00],

            // منطقة قلقيلية
            ['name' => 'حبلة', 'delivery_price' => 12.00],
            ['name' => 'جيوس', 'delivery_price' => 12.00],
            ['name' => 'عزون', 'delivery_price' => 12.00],

            // منطقة سلفيت
            ['name' => 'دير استيا', 'delivery_price' => 11.00],
            ['name' => 'كفر الديك', 'delivery_price' => 11.00],
            ['name' => 'بروقين', 'delivery_price' => 11.00],

            // منطقة بيت لحم
            ['name' => 'الخضر', 'delivery_price' => 11.00],
            ['name' => 'العبيدية', 'delivery_price' => 11.00],
            ['name' => 'تقوع', 'delivery_price' => 11.00],
            ['name' => 'الدوحة', 'delivery_price' => 11.00],

            // منطقة أريحا
            ['name' => 'العوجا', 'delivery_price' => 13.00],
            ['name' => 'عين السلطان', 'delivery_price' => 13.00],

            // مدن أخرى
            ['name' => 'أبو ديس', 'delivery_price' => 10.00],
            ['name' => 'العيزرية', 'delivery_price' => 10.00],
            ['name' => 'السواحرة', 'delivery_price' => 11.00],
            ['name' => 'بيت حنينا', 'delivery_price' => 10.00],
            ['name' => 'شعفاط', 'delivery_price' => 10.00],
            ['name' => 'سلوان', 'delivery_price' => 11.00],
            ['name' => 'الطور', 'delivery_price' => 11.00],

            // المدن الداخل الفلسطيني (48)
            ['name' => 'الناصرة', 'delivery_price' => 20.00],
            ['name' => 'حيفا', 'delivery_price' => 25.00],
            ['name' => 'عكا', 'delivery_price' => 25.00],
            ['name' => 'يافا', 'delivery_price' => 22.00],
            ['name' => 'اللد', 'delivery_price' => 20.00],
            ['name' => 'الرملة', 'delivery_price' => 20.00],
            ['name' => 'أم الفحم', 'delivery_price' => 22.00],
            ['name' => 'الطيبة', 'delivery_price' => 20.00],
            ['name' => 'باقة الغربية', 'delivery_price' => 21.00],
            ['name' => 'الطيرة', 'delivery_price' => 20.00],
            ['name' => 'كفر قاسم', 'delivery_price' => 20.00],
            ['name' => 'سخنين', 'delivery_price' => 22.00],
            ['name' => 'شفاعمرو', 'delivery_price' => 23.00],
            ['name' => 'عرابة', 'delivery_price' => 22.00],
            ['name' => 'كفر كنا', 'delivery_price' => 21.00],
            ['name' => 'دير حنا', 'delivery_price' => 22.00],
        ];

        DB::table('cities')->insert($cities);
    }
}
