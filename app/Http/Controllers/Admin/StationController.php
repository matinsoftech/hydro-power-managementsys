<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Station\StationStoreRequest;
use App\Http\Requests\Station\StationUpdateRequest;
use App\Models\Station;
use Illuminate\Http\Request;

class StationController extends Controller
{
    public function index()
    {
        $stations = Station::with('childs.childs.childs')->where('station_level',1)->get();
        return view('admin.station.index',compact('stations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.station.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StationStoreRequest $request)
    {
        $validatedData = $this->prepareData($request->validated());
        Station::create($validatedData);
        return response()->json(['status' => true,'message'=> 'Station created successfully.','url' => route('admin.station.index')]);
    }

    protected function prepareData(array $validatedData)
    {
        return array_merge($validatedData, [
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Station $station)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Station $station)
    {
        return view('admin.station.edit',compact('station'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StationUpdateRequest $request, Station $station)
    {
        $validatedData = $this->prepareData($request->validated());
        $station->update($validatedData);
        return response()->json(['status' => true,'message'=> 'Station updated successfully.','url'=>route('admin.station.index')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Station $station)
    {
        $this->deleteStationAndChilds($station);
        return response()->json(['status' => true,'message'=> 'Station deleted successfully.','url'=>route('admin.station.index')]);
    }

    private function deleteStationAndChilds($station)
    {
        foreach ($station->childs as $child) {
            $this->deleteStationAndChilds($child); // Recursively delete child stations
        }

        $station->delete(); // Delete the current station
    }

    public function getSubStation(Request $request)
    {
        $query = Station::query();
        $parentLevel = $request->level - 1;
        $subStations = $query->where('station_level', $parentLevel)->get();
        return response()->json(['status'=>true,'data'=>$subStations]);
    }
}
