<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class MakaniLocatorService
{
    public function fromMakani(string $makani): array
    {
        $makani = preg_replace('/\D/', '', $makani);

        return Cache::remember('makani:' . $makani, now()->addDay(), function () use ($makani) {
            $data = $this->fetchMakaniData($makani);
            $info = $data['MAKANI_INFO'][0] ?? null;

            if (!$info || empty($info['LATLNG'])) {
                throw ValidationException::withMessages([
                    'makani_number' => 'Could not find this Makani number.',
                ]);
            }

            [$lat, $lng] = array_map('floatval', preg_split('/[,;\s]+/', trim($info['LATLNG'])));

            return [
                'lat'       => $lat,
                'lng'       => $lng,
                'building'  => $info['BLDG_NAME_E'] ?? $data['BLDG_NAME_E'] ?? null,
                'community' => $info['COMMUNITY_E'] ?? $data['COMMUNITY_E'] ?? null,
            ];
        });
    }

    private function fetchMakaniData(string $makani): array
    {
        $base = rtrim(config('services.makani.url'), '/');


        try {
            $res = Http::timeout(15)->acceptJson()->get($base . '/GetMakaniDetails', [
                'makanino' => $makani,
                'remarks'  => 'Property verification',
            ]);
            $data = $this->decode($res->body());
            if (!empty($data['MAKANI_INFO'])) {
                return $data;
            }
            Log::warning('Makani REST lookup empty', ['status' => $res->status(), 'body' => mb_substr($res->body(), 0, 500)]);
        } catch (\Throwable $e) {
            Log::warning('Makani REST lookup error: ' . $e->getMessage());
        }

        // 2) SOAP style (needs extension=soap)
        try {
            if (class_exists(\SoapClient::class)) {
                $client = new \SoapClient($base . '?wsdl', ['exceptions' => true, 'connection_timeout' => 15]);
                $res = $client->GetMakaniDetails(['makanino' => $makani, 'remarks' => 'Property verification']);
                $data = $this->decode((string) ($res->GetMakaniDetailsResult ?? ''));
                if (!empty($data['MAKANI_INFO'])) {
                    return $data;
                }
                Log::warning('Makani SOAP lookup empty', ['body' => mb_substr(json_encode($res), 0, 500)]);
            }
        } catch (\Throwable $e) {
            Log::warning('Makani SOAP lookup error: ' . $e->getMessage());
        }

        return [];
    }

    public function fromLink(string $url): array
    {

        $url = urldecode($url);

        if (preg_match_all('~!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)~', $url, $all, PREG_SET_ORDER)) {
            $last = end($all);
            return ['lat' => (float) $last[1], 'lng' => (float) $last[2]];
        }

        if (preg_match('~(\d+)°(\d+)\'([\d.]+)"\s*([NS])\s*\+?\s*(\d+)°(\d+)\'([\d.]+)"\s*([EW])~u', $url, $m)) {
            $lat = $m[1] + $m[2] / 60 + $m[3] / 3600;
            $lng = $m[5] + $m[6] / 60 + $m[7] / 3600;
            return [
                'lat' => $m[4] === 'S' ? -$lat : $lat,
                'lng' => $m[8] === 'W' ? -$lng : $lng,
            ];
        }

        $patterns = [
            '~[?&](?:q|ll|query|destination)=(-?\d+\.\d+),(-?\d+\.\d+)~',
            '~@(-?\d+\.\d+),(-?\d+\.\d+)~',
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
