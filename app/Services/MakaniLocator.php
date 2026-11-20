<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class MakaniLocator
{
    /** Makani number -> ['lat','lng','building','community'] */
    public function fromMakani(string $makani): array
    {
        return Cache::remember('makani:' . $makani, now()->addDay(), function () use ($makani) {
            $res = Http::timeout(15)->acceptJson()->get(
                rtrim(config('services.makani.url'), '/') . '/GetMakaniDetails',
                ['makanino' => $makani, 'remarks' => 'Property verification']
            );

            $data = $this->decode($res->body());
            $info = $data['MAKANI_INFO'][0] ?? null;

            if (!$res->ok() || !$info || empty($info['LATLNG'])) {
                Log::warning('Makani lookup failed', ['makani' => $makani, 'status' => $res->status(), 'body' => $res->body()]);
                throw ValidationException::withMessages([
                    'makani_number' => 'Could not find this Makani number.',
                ]);
            }

            [$lat, $lng] = array_map('floatval', explode(',', $info['LATLNG']));

            return [
                'lat'       => $lat,
                'lng'       => $lng,
                'building'  => $data['BLDG_NAME_E'] ?? null,
                'community' => $data['COMMUNITY_E'] ?? null,
            ];
        });
    }

    /** Google Maps link -> ['lat','lng'] */
    public function fromLink(string $url): array
    {
        // Short links must be followed to their final URL
        if (preg_match('~(goo\.gl|maps\.app)~i', $url)) {
            $final = $url;
            Http::timeout(15)->withOptions([
                'allow_redirects' => true,
                'on_stats' => function ($stats) use (&$final) {
                    $final = (string) $stats->getEffectiveUri();
                },
            ])->get($url);
            $url = $final;
        }
        $url = urldecode($url);

        $patterns = [
            '~!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)~',                    // pin position (most accurate)
            '~@(-?\d+\.\d+),(-?\d+\.\d+)~',                        // map centre
            '~[?&](?:q|ll|query|destination)=(-?\d+\.\d+),(-?\d+\.\d+)~',
        ];
        foreach ($patterns as $p) {
            if (preg_match($p, $url, $m)) {
                return ['lat' => (float) $m[1], 'lng' => (float) $m[2]];
            }
        }

        throw ValidationException::withMessages([
            'location' => 'Could not read coordinates from this link. Drop a pin, tap Share and paste that link.',
        ]);
    }

    public static function distanceMeters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $r = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        return $r * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    /** The service may return JSON, JSON-in-a-string, or wrap it in a *Result key */
    private function decode(string $body): array
    {
        $json = json_decode($body, true);
        if (is_array($json)) {
            foreach ($json as $k => $v) {
                if (is_string($k) && str_ends_with($k, 'Result') && is_string($v)) {
                    $json = json_decode($v, true) ?? [];
                    break;
                }
            }
        }
        if (is_string($json)) {
            $json = json_decode($json, true);
        }
        return is_array($json) ? $json : [];
    }
}
