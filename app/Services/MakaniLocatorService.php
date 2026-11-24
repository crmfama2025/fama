<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class MakaniLocatorService
{
    /**
     * Get coordinates using the 10-digit Makani number.
     */
    public function fromMakani(string $makani): array
    {
        $makani = preg_replace('/\D/', '', $makani);

        if (strlen($makani) !== 10) {
            throw ValidationException::withMessages([
                'makani_number' => 'Makani number must be 10 digits.',
            ]);
        }

        return Cache::remember(
            'makani:' . $makani,
            now()->addDay(),
            function () use ($makani) {

                $base = rtrim(config('services.makani.url'), '/');

                try {
                    // Call Makani SOAP API
                    $client = new \SoapClient($base . '?wsdl', [
                        'exceptions' => true,
                        'trace' => true,
                        'connection_timeout' => 15,
                        'cache_wsdl' => WSDL_CACHE_MEMORY,
                    ]);

                    $response = $client->GetMakaniDetails([
                        'makanino' => $makani,
                        'remarks' => '',
                    ]);

                    $result = $response->GetMakaniDetailsResult ?? null;

                    if (is_string($result)) {
                        $result = json_decode($result, true);
                    }

                    if (is_object($result)) {
                        $result = json_decode(json_encode($result), true);
                    }

                    $info = $result['MAKANI_INFO'][0] ?? null;

                    if (!$info || empty($info['LATLNG'])) {
                        throw new \RuntimeException(
                            'Makani number was not found or coordinates are missing.'
                        );
                    }

                    $coordinates = explode(',', $info['LATLNG']);

                    if (count($coordinates) !== 2) {
                        throw new \RuntimeException(
                            'Invalid coordinates returned by Makani API.'
                        );
                    }

                    return [
                        'lat' => (float) trim($coordinates[0]),
                        'lng' => (float) trim($coordinates[1]),
                        'building' => $info['BUILDING_NAME'] ?? null,
                        'community' => $info['COMMUNITY'] ?? null,
                    ];
                } catch (\Throwable $e) {
                    Log::error('Makani API error', [
                        'makani' => $makani,
                        'message' => $e->getMessage(),
                    ]);

                    throw ValidationException::withMessages([
                        'makani_number' => 'Unable to verify Makani number. Please try again.',
                    ]);
                }
            }
        );
    }

    /**
     * Extract coordinates from Google Maps links.
     */
    public function fromLink(string $url): array
    {
        $url = urldecode($url);

        if (preg_match_all('~!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)~', $url, $all, PREG_SET_ORDER)) {
            $last = end($all);

            return [
                'lat' => (float) $last[1],
                'lng' => (float) $last[2],
            ];
        }

        if (preg_match('~@(-?\d+\.\d+),(-?\d+\.\d+)~', $url, $m)) {
            return [
                'lat' => (float) $m[1],
                'lng' => (float) $m[2],
            ];
        }

        if (preg_match('~[?&](?:q|ll|query|destination)=(-?\d+\.\d+),(-?\d+\.\d+)~', $url, $m)) {
            return [
                'lat' => (float) $m[1],
                'lng' => (float) $m[2],
            ];
        }

        throw ValidationException::withMessages([
            'location_link' => 'Could not read coordinates from this link. Drop a pin, tap Share and paste that link.',
        ]);
    }

    /**
     * Calculate distance between two coordinates.
     */
    public static function distanceMeters(
        float $lat1,
        float $lng1,
        float $lat2,
        float $lng2
    ): float {
        $r = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1))
            * cos(deg2rad($lat2))
            * sin($dLng / 2) ** 2;

        return $r * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
