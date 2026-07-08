<?php
// app/Services/CustomerRiskService.php

namespace App\Services;

use App\Models\Order;

class CustomerRiskService
{
    const RED_RATE_THRESHOLD = 30.0;  // نسبة رفض 30% فأكثر = أحمر
    const RED_MIN_COUNT = 3;          // أو 3 حالات رفض فأكثر = أحمر بغض النظر عن النسبة
    const MIN_ORDERS_FOR_RATING = 2;  // أقل من هيك = عميل جديد، ما في تاريخ كافي

    /**
     * حساب درجة الثقة لرقم هاتف واحد (لاستخدامها عند إنشاء طلب جديد مثلاً)
     */
    public static function getStats(string $phone): array
    {
        $dispatched = Order::where('customer_phone', $phone)
            ->whereNotNull('track_number')
            ->count();

        $returned = Order::where('customer_phone', $phone)
            ->where('returned_after_dispatch', true)
            ->count();

        return self::buildResult($phone, $dispatched, $returned);
    }

    /**
     * نفس الشي بس بالجملة لعدة أرقام مرة وحدة (لتفادي N+1 query بقوائم الطلبات)
     */
    public static function getStatsForPhones(array $phones): array
    {
        $phones = array_values(array_unique($phones));
        if (empty($phones)) return [];

        $dispatched = Order::whereIn('customer_phone', $phones)
            ->whereNotNull('track_number')
            ->selectRaw('customer_phone, COUNT(*) as c')
            ->groupBy('customer_phone')
            ->pluck('c', 'customer_phone');

        $returned = Order::whereIn('customer_phone', $phones)
            ->where('returned_after_dispatch', true)
            ->selectRaw('customer_phone, COUNT(*) as c')
            ->groupBy('customer_phone')
            ->pluck('c', 'customer_phone');

        $result = [];
        foreach ($phones as $phone) {
            $result[$phone] = self::buildResult(
                $phone,
                (int) ($dispatched[$phone] ?? 0),
                (int) ($returned[$phone] ?? 0)
            );
        }
        return $result;
    }

    private static function buildResult(string $phone, int $dispatched, int $returned): array
    {
        $rate = $dispatched > 0 ? round(($returned / $dispatched) * 100, 1) : 0;
        $level = self::calculateLevel($dispatched, $returned, $rate);

        return [
            'phone' => $phone,
            'dispatched_count' => $dispatched,
            'returned_count' => $returned,
            'return_rate' => $rate,
            'level' => $level,       // green | yellow | red | new
            'label' => self::levelLabel($level),
        ];
    }

    private static function calculateLevel(int $dispatched, int $returned, float $rate): string
    {
        if ($dispatched < self::MIN_ORDERS_FOR_RATING) {
            return 'new';
        }
        if ($returned >= self::RED_MIN_COUNT || $rate >= self::RED_RATE_THRESHOLD) {
            return 'red';
        }
        if ($returned > 0) {
            return 'yellow';
        }
        return 'green';
    }

    private static function levelLabel(string $level): string
    {
        return match ($level) {
            'green' => 'موثوق',
            'yellow' => 'متوسط - يفضل التأكيد',
            'red' => 'خطر - راجع الطلب يدوياً قبل الإرسال',
            'new' => 'عميل جديد',
            default => 'غير معروف',
        };
    }
}