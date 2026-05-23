<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SabeqService
{
    private function authToken()
    {
        $response = Http::post('https://sabeq.ps/api/v1/auth', [
            'login_token' => env('SABEQ_LOGIN_TOKEN'),
        ]);

        return $response->json('auth_token');
    }

    private function verificationToken()
    {
        $authToken = $this->authToken();

        $response = Http::post('https://sabeq.ps/api/v1/verify_business', [
            'auth_token' => $authToken,
            'profile_id' => env('SABEQ_PROFILE_ID'),
            'api_key' => env('SABEQ_API_KEY'),
        ]);

        return $response->json('verification_token');
    }

    public function getAreas()
    {
        // $verificationToken = $this->verificationToken();

        // $response = Http::get('https://sabeq.ps/api/v1/parcels/get_areas', [
        //     'verification_token' => $verificationToken,
        // ]);

        // return $response->json();


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

    $response = Http::post('https://sabeq.ps/api/v1/parcels', [

        'verification_token' => $verificationToken,

        'name' => $order->customer_name,

        'phone1' => $order->customer_phone,

        'phone2' => $order->customer_phone,
        // content => order items with quantities and prices

        'content' => collect($order->items)->map(function ($item) {
            return "{$item['product_name']} - (المقاس: {$item['size']}) x {$item['quantity']} (السعر: {$item['price']})";
        })->implode(', '),

        'payment_amount' => $order->total,

        'area_id' => $area_id,

        'street_id' => $street_id ?? '',

        'address' => $order->address,

        'location_url' => $order->location_url ?? '',

        'delivery_notes' => $order->delivery_notes ?? '',

        'special_notes' => '',

        'service_type' => 'pay_delivery',

    ]);

    return $response->json();
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
}