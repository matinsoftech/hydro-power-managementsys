<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Grid;
use App\Models\User;
use App\Models\Fault;
use App\Models\MeterReading;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AnalyticsController extends Controller
{
    public function index()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $usersCount = User::where('user_type','User')->count();

        $meterReadingCount = MeterReading::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->count();
        $totalFaults = Fault::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                        ->count();
        $totalGridCount = Grid::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        return response()->json([
            'usersCount' => $usersCount,
            'meterReadingCount' => $meterReadingCount,
            'totalFaults' => $totalFaults,
            'totalGridCount' => $totalGridCount
        ]);
    }
}
