<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeoService
{
    protected $endpoint;

    public function __construct()
    {
        // Set the endpoint for the receiving app
        $this->endpoint = 'https://hsj.plaza.solar/api/user/address/import';
    }

    public function sendData($givenLat, $givenLong, $radius, $filter)
    {
        $response = Http::post($this->endpoint, [
            'lat' => $givenLat,
            'long' => $givenLong,
            'radius' => $radius,
            'filter' => $filter
        ]);

        if ($response->failed()) {
            // Handle failures, throw exceptions, etc.
            return [
                'lat' => $givenLat,
                'long' => $givenLong,
                'radius' => $radius
            ];
            throw new \Exception("Failed to retrieve data from the geo service.");
        }

        return $response->json();
    }

    public function saveData($data)
    {
        $response = Http::post($this->endpoint, [
            'name' => $data['name'],
            'street_name' => $data['street_address'],
            'city' => $data['city'],
            'state' => $data['state'],
            'zip' => $data['zip'],
            'tags' => $data['tags'] ,  // Use null-coalescing operator to handle possible unset fields
            'location' => $data['location'] ,
            'email' => $data['email'],
            'link' => $data['link'],
        ]);
//        dump url sent to
        dump($response->json());
    }
}
