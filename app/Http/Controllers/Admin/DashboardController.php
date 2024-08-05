<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Station;
use App\Models\User;
use App\Models\Fault;
use App\Models\MeterReading;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $stations = Station::with('childs.childs.childs')->where('station_level',1)->get();
        $TotalUsers=User::count();
        $TotalFault=Fault::count();
        $TotalMeterReading=MeterReading::count();
        // return $TotalUsers;
        return view('admin.dashboard',compact('stations','TotalUsers','TotalFault','TotalMeterReading'));
    }
}
