<?php

namespace App\Http\Controllers\Admin;

use App\Models\Holiday;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Http\Requests\Holiday\HolidayStoreRequest;
use App\Http\Requests\Holiday\HolidayUpdateRequest;

class HolidayController extends Controller
{
    public function index(Request $request)
    {
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
        return view('admin.holiday.index');
    }

    public function create()
    {
        return view('admin.holiday.create');
    }

    public function store(HolidayStoreRequest $request)
    {
        Holiday::create($request->validated());
        return response(['status' => true, 'message' => 'Holiday added successfully', 'url' => route('admin.holiday.index')]);
    }

    public function show(Holiday $holiday)
    {
        return view('admin.holiday.show', compact('holiday'));
    }

    public function edit(Holiday $holiday)
    {
        return view('admin.holiday.edit', compact('holiday'));
    }

    public function update(HolidayUpdateRequest $request, Holiday $holiday)
    {
        $holiday->update($request->validated());
        return response(['status' => true, 'message' => 'Holiday updated successfully', 'url' => route('admin.holiday.index')]);
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();
        return response(['status' => true, 'message' => 'Holiday Deleted Successfully']);
    }
}
