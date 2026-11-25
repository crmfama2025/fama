<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class MakaniLocatorService
{
    /**
     * Follow Google short links (maps.app.goo.gl) to the full URL.
     * Only Google short-link hosts are followed.
     */
    public function resolveLink(string $url): string
    {
        if (!preg_match('~^https?://(maps\.app\.goo\.gl|goo\.gl/maps)~i', $url)) {
            return $url;
        }


        for ($i = 0; $i < 5; $i++) {
            $response = Http::withoutRedirecting()
                ->withOptions(['force_ip_resolve' => 'v4'])
                ->timeout(10)
                ->get($url);

            $next = $response->header('Location');

            if (!$next) {
                break;
            }
            $url = $next;

            if (!preg_match('~^https?://(maps\.app\.goo\.gl|goo\.gl/maps)~i', $url)) {
                break;
            }
        }

        return $url;
    }

    /**
     * Extract coordinates from a Google Maps link.
     */
    public function fromLink(string $url): array
    {
        $url = urldecode($this->resolveLink($url));

        if (preg_match_all('~!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)~', $url, $all, PREG_SET_ORDER)) {
            $last = end($all);

            return ['lat' => (float) $last[1], 'lng' => (float) $last[2]];
        }

        if (preg_match('~@(-?\d+\.\d+),(-?\d+\.\d+)~', $url, $m)) {
            return ['lat' => (float) $m[1], 'lng' => (float) $m[2]];
        }

        if (preg_match('~[?&](?:q|ll|query|destination)=(-?\d+\.\d+),(-?\d+\.\d+)~', $url, $m)) {
            return ['lat' => (float) $m[1], 'lng' => (float) $m[2]];
        }

        throw ValidationException::withMessages([
            'location_link' => 'Could not read coordinates from this link. Drop a pin, tap Share and paste that link.',
        ]);
    }

    /**
     * Reverse geocode with Google: address + is the point inside Dubai?
     */
    public function reverseGeocode(float $lat, float $lng): array
    {
        $json = Http::timeout(10)
            ->withOptions(['force_ip_resolve' => 'v4'])
            ->get('https://maps.googleapis.com/maps/api/geocode/json', [
                'latlng' => "{$lat},{$lng}",
                'key'    => config('services.google.geocode_key'),
            ])
            ->json();

        if (($json['status'] ?? '') !== 'OK' || empty($json['results'][0])) {
            Log::error('Google geocode error', [
                'status'      => $json['status'] ?? null,
                'message'     => $json['error_message'] ?? null,
                'lat'         => $lat,
                'lng'         => $lng,
                'key_present' => filled(config('services.google.geocode_key')),
            ]);

            throw ValidationException::withMessages([
                'location_link' => 'Could not locate this pin on the map. Please check the link.',
            ]);
        }

        $first   = $json['results'][0];
        $country = null;
        $emirate = null;

        foreach ($first['address_components'] as $c) {
            if (in_array('country', $c['types'])) {
                $country = $c['short_name'];
            }
            if (in_array('administrative_area_level_1', $c['types'])) {
                $emirate = $c['long_name'];
            }
        }

        return [
            'address'  => $first['formatted_address'],
            'in_dubai' => $country === 'AE' && stripos((string) $emirate, 'Dubai') !== false,
        ];
    }
}
