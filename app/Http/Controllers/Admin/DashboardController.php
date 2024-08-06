<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Station;
use App\Models\User;
use App\Models\Fault;
use App\Models\MeterReading;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;


class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $stations = Station::with('childs.childs.childs')->where('station_level', 1)->get();
        $TotalUsers = User::count();
        $TotalFault = Fault::count();
        $TotalMeterReading = MeterReading::count();

        // Get today's date
        $today = date('Y-m-d');

        // Get today's meter readings
        $todaysMeterReadings = MeterReading::with('createdBy')->whereDate('date', $today)->get();

        // dd($todaysMeterReadings);
        // Pass today's meter readings to the view
        return view('admin.dashboard', compact('stations', 'TotalUsers', 'TotalFault', 'TotalMeterReading', 'todaysMeterReadings'));
    }

}
