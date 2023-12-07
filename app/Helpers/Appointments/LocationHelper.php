<?php

namespace App\Helpers\Appointments;

use App\Models\SalesFlow\Lead\Lead;
use Illuminate\Support\Facades\Cache;

class LocationHelper
{
    public static function getCustomerLatLngFromAddress(Lead $lead)
    {
        $address = $lead->customer->street_address . '+' . $lead->customer->city;
        return self::getCustomerLatLng($address);
    }

    private static function getCustomerLatLng($address)
    {
        $client = new \GuzzleHttp\Client();
        $request = $client->get("https://maps.googleapis.com/maps/api/geocode/json?address=" . $address . "&key=AIzaSyAUyWQcN-DzrSx7-DU2nG1ppCfP378Hbi8");
        $response = $request->getBody();
        $payload = json_decode($response);
        return $payload->results[0]->geometry->location;
    }

    public static function computeDistance($lat1, $lng1, $lat2, $lng2 )
    {
      return  Cache::remember("distance.$lat1.$lat2.$lng1.$lng2", 60, function () use ($lat1, $lng1, $lat2, $lng2) {
          $radius = 6378137;
            $lat1 = deg2rad($lat1);
            $lng1 = deg2rad($lng1);
            $lat2 = deg2rad($lat2);
            $lng2 = deg2rad($lng2);
            $dlat = $lat2 - $lat1;
            $dlng = $lng2 - $lng1;
            $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            $d = $radius * $c;
            return $d;
        });
        static $x = M_PI / 180;
        $lat1 *= $x;
        $lng1 *= $x;
        $lat2 *= $x;
        $lng2 *= $x;
        $distance = 2 * asin(sqrt(pow(sin(($lat1 - $lat2) / 2), 2) + cos($lat1) * cos($lat2) * pow(sin(($lng1 - $lng2) / 2), 2)));

        return $distance * $radius;
    }
}
