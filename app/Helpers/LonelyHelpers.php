<?php


namespace App\Helpers;


use Illuminate\Support\Facades\Http;

class LonelyHelpers
{
    public static function distFrom($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $dist = $earthRadius * $c;

        return $dist / 1000;
    }

    public static function getCoordinatesFromAddress(string $address) {
        $prepAddr = str_replace(' ','+',$address);

        $geocodeApiKey = \config('google_api.geocodeApiKey');
        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json?address='. $prepAddr . '&key=' . $geocodeApiKey);

        $location = json_decode($response->body())->results[0]->geometry->location;
        $lat = $location->lat;
        $lng = $location->lng;

        return [
            'latitude' => $lat,
            'longitude' => $lng
        ];

    }
}
