<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Station;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $stations = Station::with('childs.childs.childs')->where('station_level',1)->get();
        return view('admin.dashboard',compact('stations'));
    }
}
