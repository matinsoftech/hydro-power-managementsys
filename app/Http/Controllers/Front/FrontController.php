<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Station;


class FrontController extends Controller
{
    public function welcome()
    {
        return view('front.welcome');
    }

    public function map()
    {
        $stations = Station::with('childs.childs.childs')->where('station_level',1)->get();
        $googleMapsApiKey = config('services.google_maps.api_key');// or config('services.google_maps.api_key') if you set it in
        // return $googleMapsApiKey;
        return view('front.showmap',compact('stations','googleMapsApiKey'));
    }
}
