<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SabeqService
{
    private $user;

    public function __construct($user = null)
    {
        $this->user = $user;
    }
    private function authToken()
    {
        $loginToken = $this->user?->sabeq_login_token ?? env('SABEQ_LOGIN_TOKEN');
        $response = Http::post('https://sabeq.ps/api/v1/auth', [
            'login_token' => $loginToken,
        ]);

        return $response->json('auth_token');
    }
    public function updateParcel($trackNumber, array $data)
    {
        $verificationToken = $this->verificationToken();

        $payload = array_merge(
            ['verification_token' => $verificationToken],
            $data
        );

        Log::info('Sabeq Update Parcel Request', [
            'track_number' => $trackNumber,
            'payload' => $payload
        ]);

        $response = Http::patch("https://sabeq.ps/api/v1/parcels/{$trackNumber}", $payload);

        $responseData = $response->json();

        Log::info('Sabeq Update Parcel Response', [
            'status' => $response->status(),
            'data' => $responseData
        ]);

        return $responseData;
    }

private function verificationToken()
    {
        // كاش لكل مستخدم لحاله — عشان توكن مستخدم ما يروح لمستخدم ثاني
        $cacheKey = 'sabeq_verification_token_' . ($this->user?->id ?? 'default');

        return Cache::remember($cacheKey, now()->addMinutes(4), function () {
            $authToken = $this->authToken();
            $profileId = $this->user?->sabeq_profile_id ?? env('SABEQ_PROFILE_ID');
            $apiKey = $this->user?->sabeq_api_key ?? env('SABEQ_API_KEY');

            $response = Http::post('https://sabeq.ps/api/v1/verify_business', [
                'auth_token' => $authToken,
                'profile_id' => $profileId,
                'api_key' => $apiKey,
            ]);

            $token = $response->json('verification_token');

            // لا تخزّن فشل — لو التوكن ما رجع، ارمِ exception عشان الكاش ما ينحفظ
            if (empty($token)) {
                throw new \Exception('Sabeq verification failed: ' . $response->body());
            }

            return $token;
        });
    }

    public function getAreas()
    {
        $cacheKey = 'sabeq_areas';
        $areas = Cache::get($cacheKey);
        if (!$areas) {
            try {
                $verificationToken = $this->verificationToken();

                $response = Http::get('https://sabeq.ps/api/v1/parcels/get_areas', [
            'verification_token' => $verificationToken,
        ]);

                $areas = $response->json();
                Cache::put($cacheKey, $areas, now()->addHours(6));
            } catch (\Exception $e) {
                Log::error('Error fetching Sabeq areas: ' . $e->getMessage());
            }
        }

        return $areas;
    }
    public function createParcel($order, $area_id, $street_id)
    {
        $verificationToken = $this->verificationToken();

        $content = collect($order->items)->map(function ($item) {
            $content = $item['product_name'];

            if (array_key_exists('size', $item) && !empty($item['size'])) {
                $content .= " - (المقاس: {$item['size']})";
            }

            $content .= " x {$item['quantity']}";

            return $content;
        })->implode(', ');

        $payload = [
            'verification_token' => $verificationToken,
            'name' => $order->customer_name,
            'phone1' => $order->customer_phone,
            'phone2' => $order->customer_phone2 ?: '',
            'content' => $content,
            'payment_amount' => $order->total,
            'area_id' => $area_id,
            'street_id' => $street_id ?? '',
            'address' => $order->address,
            'location_url' => $order->location_url ?? '',
            'delivery_notes' => $order->description ?? '',
            'special_notes' => '',
            'service_type' => 'pay_delivery',
        ];

        Log::info('Sabeq API Request', ['payload' => $payload]);

        $response = Http::post('https://sabeq.ps/api/v1/parcels', $payload);

        $responseData = $response->json();

        Log::info('Sabeq API Response', [
            'status' => $response->status(),
            'data' => $responseData
        ]);

        return $responseData;
    }
    public function createParcelUser($order, $area_id, $street_id)
    {
        $verificationToken = $this->verificationToken();

        $content = collect($order->items)->map(function ($item) {
            $content = $item['description'];
            return $content;
        })->implode(', ');

        // Map Arabic service types to Sabeq API values
        $serviceTypeMap = [
            'تسليم وتحصيل' => 'pay_delivery',
            'تبديل طرد' => 'exchange',
            'تسليم فقط' => 'deliver_only',
            'استلام طرد' => 'fetch'
        ];

        $sabeqServiceType = $serviceTypeMap[$order->service_type] ?? 'pay_delivery';

        $payload = [
            'verification_token' => $verificationToken,
            'name' => $order->customer_name,
            'phone1' => $order->customer_phone,
            'phone2' => $order->customer_phone,
            'content' => $content,
            'payment_amount' => $order->total,
            'area_id' => $area_id,
            'street_id' => $street_id ?? '',
            'address' => $order->address,
            'location_url' => $order->location_url ?? '',
            'delivery_notes' => $order->delivery_notes ?? '',
            'special_notes' => '',
            'service_type' => $sabeqServiceType,
        ];

        Log::info('Sabeq API Request', ['payload' => $payload]);

        $response = Http::post('https://sabeq.ps/api/v1/parcels', $payload);

        $responseData = $response->json();

        Log::info('Sabeq API Response', [
            'status' => $response->status(),
            'data' => $responseData
        ]);

        return $responseData;
    }
    public function informationParcel($trackNumber)
    {
        $verificationToken = $this->verificationToken();

        $response = Http::get("https://sabeq.ps/api/v1/parcels/{$trackNumber}", [
            'verification_token' => $verificationToken,
        ]);

        return $response->json();
    }
        public function cancelParcel($trackNumber)
    {
        $verificationToken = $this->verificationToken();

        $response = Http::get("https://sabeq.ps/api/v1/parcels/{$trackNumber}/cancel", [
            'verification_token' => $verificationToken,
        ]);

        return $response->json();
    }
    public function markAsReady($trackNumber)
    {
    $verificationToken = $this->verificationToken();

    $response = Http::post("https://sabeq.ps/api/v1/parcels/{$trackNumber}/packed_ready", [
        'verification_token' => $verificationToken,
    ]);

    return $response->json();

    }
    public function printParcel($trackNumber, $size = '10x10')
{
    $verificationToken = $this->verificationToken();
    
    $response = Http::get("https://sabeq.ps/api/v1/parcels/{$trackNumber}/print", [
        'verification_token' => $verificationToken,
        'size' => $size,
    ]);
    
    return $response->body(); // HTML content
}
}