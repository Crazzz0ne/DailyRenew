<?php

namespace App\Http\Controllers\Api\SalesFlow\LocationServices;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class LocationController extends Controller
{
	//    https://maps.googleapis.com/maps/api/geocode/json?latlng=32.7589082,-117.1463884&location_type=ROOFTOP&result_type=street_address&key=AIzaSyAUyWQcN-DzrSx7-DU2nG1ppCfP378Hbi8

	public function getLocation(Request $request)
	{
		if (!$request->rt) {
			return response()->json('No token no cookie');
		} else {
			if ($request->lat && $request->lng) {
				$lat = $request->lat;
				$lng = $request->lng;

				$client   = new Client(); //GuzzleHttp\Client
				$response = $client->request('GET',
					'https://maps.googleapis.com/maps/api/geocode/json?latlng=' .
					$lat . ',' . $lng .
					'&location_type=ROOFTOP&result_type=street_address&key=AIzaSyAUyWQcN-DzrSx7-DU2nG1ppCfP378Hbi8');

				return json_decode($response->getBody(), true);
			} else {
				return response()->json('Something went wrong, lat and lng are required');
			}

		}
	}
}
