<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;


class HotelAuthController extends Controller
{
    public function authenticate(Request $request)
    {
        $request->validate([
            'domain'  => 'required|string',
            'api_key' => 'required|string',
        ]);

        $jsonPath = base_path('hotel.json');

        if (!File::exists($jsonPath)) {
            return response()->json([
                'success' => false,
                'message' => 'something went wrong, hotel not found'
            ], 500);
        }

        $hotels = json_decode(File::get($jsonPath), true);

        $foundHotel = collect($hotels)->first(function ($hotel) use ($request) {
            return isset($hotel['hotel_domain'], $hotel['api_key']) &&
                rtrim($hotel['hotel_domain'], '/') === rtrim($request->domain, '/') &&
                $hotel['api_key'] === $request->api_key;
        });
        
        if ($foundHotel) {
            return response()->json([
                'success'     => true,
                'message'     => 'Authentication successful',
                'domain'      => $foundHotel['hotel_domain'],
                'token'       => $foundHotel['api_key'],
                'hotel_name'  => $foundHotel['hotel_name'],
                'hotel_logo'  => $foundHotel['hotel_logo'],
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Authentication failed',
        ], 401);


        return response()->json([
            'success' => false,
            'message' => 'Invalid domain or API key',
            'domain'  => ''
        ], 401);
    }
}
