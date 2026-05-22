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
}