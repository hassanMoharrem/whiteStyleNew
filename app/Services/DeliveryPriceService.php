<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeliveryPriceService
{
    /**
     * Calculate delivery price based on city name (English)
     *
     * @param string|null $cityNameEn
     * @return float
     */
public static function calculatePrice(?string $cityNameEn): float
{
    if (empty($cityNameEn)) {
        return config('delivery.default_price', 19);
    }

    // Normalize city name to lowercase for comparison
    $cityNameEn = strtolower(trim($cityNameEn));

    // Get prices from config
    $prices = config('delivery.prices', []);

    // Direct match
    if (isset($prices[$cityNameEn])) {
        return (float) $prices[$cityNameEn];
    }

    // Partial matching for variations
    if (str_contains($cityNameEn, 'israel')) {
        return 70;
    }

    if (str_contains($cityNameEn, 'jerusalem (')) {
        return 30;
    }

    if (str_contains($cityNameEn, 'abo gosh')) {
        return 45;
    }

    // Default price (West Bank cities)
    return config('delivery.default_price', 19);
}

    /**
     * Get city_name_en from Sabeq API by city_name
     *
     * @param string $cityName
     * @return string|null
     */
    public static function getCityNameEnFromSabeq(string $cityName): ?string
    {
        try {
            $sabeqService = new SabeqService();
            $areas = $sabeqService->getAreas();

            if (is_array($areas)) {
                foreach ($areas as $city) {
                    if (isset($city['city_name']) && trim($city['city_name']) === trim($cityName)) {
                        return $city['city_name_en'] ?? null;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to get city_name_en from Sabeq: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Calculate delivery price by fetching city_name_en from Sabeq
     *
     * @param string $cityName
     * @return float
     */
    public static function calculatePriceFromCityName(string $cityName): float
    {
        $cityNameEn = self::getCityNameEnFromSabeq($cityName);
        return self::calculatePrice($cityNameEn);
    }
}
