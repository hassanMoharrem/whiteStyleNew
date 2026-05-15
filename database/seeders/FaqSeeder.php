<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Faq;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'ما هي خيارات الشحن المتاحة؟',
                'answer' => 'نوفر الشحن العادي (5-7 أيام عمل) والشحن السريع (2-3 أيام عمل). الشحن مجاني للطلبات التي تزيد عن 200 شيكل.',
                'visible' => true,
            ],
            [
                'question' => 'ما هي سياسة الإرجاع؟',
                'answer' => 'نقبل الإرجاع خلال 30 يوماً من الشراء. يجب أن تكون المنتجات غير مستخدمة وفي عبوتها الأصلية. الرجاء التواصل مع خدمة العملاء لبدء عملية الإرجاع.',
                'visible' => true,
            ],
            [
                'question' => 'كيف يمكنني تتبع طلبي؟',
                'answer' => 'بمجرد شحن طلبك، سيتم إرسال رقم التتبع إليك عبر البريد الإلكتروني. يمكنك استخدام هذا الرقم لتتبع الطرد على موقعنا أو موقع شركة الشحن.',
                'visible' => true,
            ],
            [
                'question' => 'هل تشحنون للخارج؟',
                'answer' => 'نعم، نشحن لمعظم الدول حول العالم. تختلف أسعار ومواعيد الشحن الدولي حسب الوجهة. الرجاء مراجعة صفحة الشحن لمزيد من التفاصيل.',
                'visible' => true,
            ],
            [
                'question' => 'ما هي طرق الدفع المقبولة؟',
                'answer' => 'نقبل جميع البطاقات الائتمانية الرئيسية (فيزا، ماستركارد، أمريكان إكسبريس)، باي بال، وآبل باي. جميع المعاملات آمنة ومشفرة.',
                'visible' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}
