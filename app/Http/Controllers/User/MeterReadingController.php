<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\MeterReading;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Http\Requests\MeterReading\MeterReadingStoreRequest;
use App\Http\Requests\MeterReading\MeterReadingUpdateRequest;

class MeterReadingController extends Controller
{
    public function __construct()
    {
        $this->middleware('sys_entry')->only(['store','update','destroy']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = MeterReading::query();
            return DataTables::of($query->with('createdBy'))
                ->addIndexColumn()
                ->editColumn('created_by', function ($row) {
                    return $row->createdBy->name ?? 'No user';
                })
                ->editColumn('time', function ($row) {
                    if(Carbon::parse($row->time)->addHour()->format('H:i') == '00:00'){
                        return '24:00';
                    }else{
                        return Carbon::parse($row->time)->addHour()->format('H:i');
                    }
                })
                ->editColumn('created_by',function ($row){
                    return $row->createdBy->name ?? 'No user';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="button-group" role="group">
                                <a class="btn btn-sm btn-primary" href="' . route('admin.meter-reading.edit', $row->id) . '"><i class="fa fa-edit"></i></a>
                                <button class="btn btn-sm btn-danger btnDelete"  data-url="' . route('admin.meter-reading.destroy', $row->id) . '"><i class="fa fa-x"></i></button>
                            </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.meter-reading.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $meterReadings = MeterReading::where('date', date('Y-m-d'))->get()->keyBy('time');
        return view('admin.meter-reading.create', compact('meterReadings'));
    }

    public function detail(Request $request)
    {
        if($request->date){
            $date = $request->date;
            $meterReadings = MeterReading::where('date', $date)->get()->keyBy('time');
            return view('admin.meter-reading.detail',compact('meterReadings','date'));
        }
        return view('admin.meter-reading.detail');
    }

    public function apiDetail(Request $request)
    {
        $request->validate(['date' => 'required|date']);
        $date = $request->date;
        $meterReadings = MeterReading::where('date', $date)->get()->keyBy('time');
        return response()->json($meterReadings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MeterReadingStoreRequest $request)
    {
        $validatedData = $request->validated();
        if($request->date){
            $date = $request->date;
        }else{
            $date = now()->format('Y-m-d');
        }
        $userId = auth()->id();

        foreach ($validatedData['time'] as $index => $time) {
            if (!empty($validatedData['main_meter'][$index]) || !empty($validatedData['show_meter'][$index]) || !empty($validatedData['remarks'][$index])) {
                MeterReading::updateOrCreate(
                    [
                        'created_by' => $userId,
                        'date' => $date,
                        'time' => $time,
                    ],
                    [
                        'main_meter' => $validatedData['main_meter'][$index] ?? null,
                        'show_meter' => $validatedData['show_meter'][$index] ?? null,
                        'remarks' => $validatedData['remarks'][$index] ?? null,
                    ]
                );
            }
        }

        return response()->json(['status' => true, 'message' => 'Meter readings added successfully', 'url' => route('admin.meter-reading.index')]);
    }

    /**
     * Display the specified resource.
     */
    public function show(MeterReading $meterReading)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MeterReading $meterReading)
    {
        $date = $meterReading->date;
        $meterReadings = MeterReading::where('date', $date)->get()->keyBy('time');
        return view('admin.meter-reading.edit', compact('meterReadings','date'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MeterReadingUpdateRequest $request, MeterReading $meterReading)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MeterReading $meterReading)
    {
        $meterReading->delete();
        return response(['status' => true, 'message' => 'Meter reading deleted successfully', 'url' => route('admin.meter-reading.index')]);
    }
}
