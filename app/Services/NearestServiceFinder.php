<?php

namespace App\Services;

use App\Models\EmergencyService;

class NearestServiceFinder
{
    public static function find(float $lat, float $lng, ?string $type = null, int $limit = 3): array
    {
        $query = EmergencyService::query();
        if ($type) {
            $query->where('type', $type);
        }

        return $query->get()
            ->map(function (EmergencyService $service) use ($lat, $lng) {
                $distance = self::haversineKm($lat, $lng, $service->latitude, $service->longitude);

                return [
                    'service' => $service,
                    'distance_km' => round($distance, 2),
                    'eta_min' => max(3, (int) round($distance * 3 + 2)),
                ];
            })
            ->sortBy('distance_km')
            ->take($limit)
            ->values()
            ->all();
    }

    public static function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
