<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GeocodingService
{
    protected $amapWebKey;
    protected $googleApiKey;

    public function __construct()
    {
        $this->amapWebKey = config('app.amap_web_key');
        $this->googleApiKey = config('app.google_api_key');
    }

    public function getCoordinates($alamat, $negara)
    {
        $alamat_clean = str_replace(["\r", "\n", '"', "'", "#"], " ", $alamat);
        
        $chinaKeywords = ['china', 'tiongkok', 'prc', "people's republic of china", 'cn'];
        $negara_lower = strtolower(trim($negara));
        
        $isChina = false;
        foreach ($chinaKeywords as $kw) {
            if (str_contains($negara_lower, $kw)) {
                $isChina = true;
                break;
            }
        }

        if ($isChina) {
            return $this->getCoordinatesChina($alamat_clean);
        } else {
            return $this->getCoordinatesGoogle($alamat_clean, $negara);
        }
    }

    protected function getCoordinatesChina($alamat)
    {
        // POI Search (Gaode)
        $urlPOI = "https://restapi.amap.com/v3/place/text";
        $resPOI = Http::get($urlPOI, [
            'keywords' => $alamat,
            'key' => $this->amapWebKey,
            'offset' => 1,
            'page' => 1
        ]);
        
        if ($resPOI->successful()) {
            $data = $resPOI->json();
            if (!empty($data['pois'][0]['location'])) {
                $loc = explode(',', $data['pois'][0]['location']);
                return ['lat' => (float)$loc[1], 'lng' => (float)$loc[0]];
            }
        }

        // Geocoding Fallback (Gaode)
        $urlGeo = "https://restapi.amap.com/v3/geocode/geo";
        $resGeo = Http::get($urlGeo, [
            'address' => $alamat,
            'key' => $this->amapWebKey
        ]);
        
        if ($resGeo->successful()) {
            $data = $resGeo->json();
            if (!empty($data['geocodes'][0]['location'])) {
                $loc = explode(',', $data['geocodes'][0]['location']);
                return ['lat' => (float)$loc[1], 'lng' => (float)$loc[0]];
            }
        }

        return ['lat' => 0.0, 'lng' => 0.0];
    }

    protected function getCoordinatesGoogle($alamat, $negara)
    {
        // Use Google Maps as primary if key is available
        if (!$this->googleApiKey) return ['lat' => 0.0, 'lng' => 0.0];

        $full_query = $alamat . ", " . $negara;

        // Search by location name (Google)
        $urlPlaces = "https://maps.googleapis.com/maps/api/place/textsearch/json";
        $resPlaces = Http::get($urlPlaces, [
            'query' => $full_query,
            'key' => $this->googleApiKey
        ]);
        
        if ($resPlaces->successful()) {
            $dataP = $resPlaces->json();
            if (($dataP['status'] ?? '') === 'OK' && !empty($dataP['results'][0]['geometry']['location'])) {
                return [
                    'lat' => (float)$dataP['results'][0]['geometry']['location']['lat'],
                    'lng' => (float)$dataP['results'][0]['geometry']['location']['lng']
                ];
            }
        }

        // Precise Geocoding (Google)
        $urlGeo = "https://maps.googleapis.com/maps/api/geocode/json";
        $resGeo = Http::get($urlGeo, [
            'address' => $full_query,
            'key' => $this->googleApiKey
        ]);
        
        if ($resGeo->successful()) {
            $dataG = $resGeo->json();
            if (($dataG['status'] ?? '') === 'OK' && !empty($dataG['results'][0]['geometry']['location'])) {
                return [
                    'lat' => (float)$dataG['results'][0]['geometry']['location']['lat'],
                    'lng' => (float)$dataG['results'][0]['geometry']['location']['lng']
                ];
            }
        }

        return ['lat' => 0.0, 'lng' => 0.0];
    }
}
