<?php

namespace App\Http\Controllers\User;

use App\Models\Holiday;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HolidayController extends Controller
{
    public function __construct()
    {
        $this->middleware('sys_entry')->only(['store','update','destroy']);
    }

    public function index(Request $request)
    {
        if($request->is('api/*')) return $this->apiIndex($request);
        if ($request->ajax()) {
            $start = $request->query('start');
            $end = $request->query('end');

            // Fetch holidays within the date range
            $holidays = Holiday::where(function ($query) use ($start, $end) {
                $query->where('start_date', '>=', $start)
                    ->where('start_date', '<=', $end);
            })->orWhere(function ($query) use ($start, $end) {
                $query->where('end_date', '>=', $start)
                    ->where('end_date', '<=', $end);
            })->orWhere(function ($query) use ($start, $end) {
                $query->where('start_date', '<', $start)
                    ->where('end_date', '>', $end);
            })->get(['id','title', 'start_date as start', 'end_date as end']);

            foreach ($holidays as $holiday) {
                $holiday->end = \Carbon\Carbon::parse($holiday->end)->addDay()->format('Y-m-d');
            }

            return response()->json($holidays);
        }
        return view('user.holiday.index');
    }

    public function apiIndex(Request $request)
    {
        $query = Holiday::query();
        $data = $query->get();
        return response()->json(['data'=>$data]);
    }
}
