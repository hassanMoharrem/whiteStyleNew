<?php

/**
 * الخطوة 2: WebhookController
 *
 * احفظ هذا الملف في: app/Http/Controllers/Api/SabeqWebhookController.php
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SabeqWebhookController extends Controller
{
    /**
     * الحالات المقبولة من سابق ولاحق
     */
    private const VALID_STATUSES = [
        'unknown',
        'created',
        'warehouse',
        'on_way',
        'delivered',
        'returned',
        'missing',
        'completed',
        'cancelled',
        'packed_ready',
    ];

    /**
     * استقبال إشعارات سابق ولاحق
     * POST /api/webhooks/sabeq
     *
     * قاعدة ذهبية: نرجع 200 دائماً (إلا في فشل التوقيع)
     * لأن سابق لا يعيد المحاولة، وأي خطأ منا = ضياع الإشعار.
     */
    public function handle(Request $request)
    {
        // ===== 1. التحقق من التوقيع =====
        $signature = $request->header('X-Webhook-Signature');
        $secret = config('services.sabeq.webhook_secret');

        if (empty($secret)) {
            Log::error('Sabeq webhook: SABEQ_WEBHOOK_SECRET is not configured');
            return response()->json(['status' => 'error'], 500);
        }

        // hash_equals = مقارنة آمنة ضد timing attacks
        if (empty($signature) || !hash_equals($secret, $signature)) {
            Log::warning('Sabeq webhook: invalid signature', [
                'ip' => $request->ip(),
            ]);
            return response()->json(['status' => 'unauthorized'], 401);
        }

        // ===== 2. تسجيل الإشعار كاملاً (مهم جداً للتتبع لأنه لا يوجد retry) =====
        Log::info('Sabeq webhook received', ['payload' => $request->all()]);

        // ===== 3. قراءة البيانات =====
        $trackNumber = $request->input('track_number');
        $newStatus = $request->input('status');

        if (empty($trackNumber)) {
            // نرجع 200 حتى لا نسبب مشاكل — لكن نسجل
            Log::warning('Sabeq webhook: missing track_number');
            return response()->json(['status' => 'ok']);
        }

        // ===== 4. إيجاد الطلب =====
        $order = Order::where('track_number', $trackNumber)->first();

        if (!$order) {
            // طرد غير موجود عندنا (ربما أُنشئ يدوياً على سابق مباشرة) — نتجاهل بهدوء
            Log::info("Sabeq webhook: no local order for track_number {$trackNumber}");
            return response()->json(['status' => 'ok']);
        }

        // ===== 5. تحديث حالة التوصيل  +  تحديث حالة الطرد لما يلتغي=====
        $updates = [];

        if (!empty($newStatus) && in_array($newStatus, self::VALID_STATUSES, true)) {
            if ($newStatus === 'cancelled' || $newStatus === 'returned') {
                // إذا أرسل سابق حالة "ملغى"، نعتبر الطلب ملغى عندنا أيضاً
                $updates['status'] = 'cancelled';
            }
            $updates['delivery_status'] = $newStatus;
            $updates['delivery_status_updated_at'] = now();
        } elseif (!empty($newStatus)) {
            // حالة جديدة غير معروفة أضافها سابق — نخزنها كما هي بدل ما نضيعها
            Log::warning("Sabeq webhook: unrecognized status '{$newStatus}' for {$trackNumber}");
            $updates['delivery_status'] = $newStatus;
            $updates['delivery_status_updated_at'] = now();
        }

        // ===== 6. تحديث المبالغ إذا تغيرت من طرف سابق =====
        // payment_amount عند سابق = total عندنا (الشامل للتوصيل)
        if ($request->filled('payment_amount')) {
            $incomingTotal = (float) $request->input('payment_amount');
            if (abs($incomingTotal - (float) $order->total) > 0.01) {
                $updates['total'] = $incomingTotal;
                // نعيد حساب الصافي بنفس معادلة النظام
                $delivery = $request->filled('service_cost')
                    ? (float) $request->input('service_cost')
                    : (float) $order->delivery_price;
                $updates['subtotal'] = $incomingTotal - $delivery;
                Log::info("Sabeq webhook: payment_amount changed for {$trackNumber}: {$order->total} -> {$incomingTotal}");
            }
        }

        if ($request->filled('service_cost')) {
            $incomingDelivery = (float) $request->input('service_cost');
            if (abs($incomingDelivery - (float) $order->delivery_price) > 0.01) {
                $updates['delivery_price'] = $incomingDelivery;
                $totalForCalc = $updates['total'] ?? (float) $order->total;
                $updates['subtotal'] = $totalForCalc - $incomingDelivery;
                Log::info("Sabeq webhook: service_cost changed for {$trackNumber}: {$order->delivery_price} -> {$incomingDelivery}");
            }
        }

        if (!empty($updates)) {
            $order->update($updates);
        }

        return response()->json(['status' => 'ok']);
    }
}