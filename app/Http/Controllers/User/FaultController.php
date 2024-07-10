<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Fault;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Http\Requests\Fault\FaultStoreRequest;
use App\Http\Requests\Fault\FaultUpdateRequest;

class FaultController extends Controller
{
    public function __construct()
    {
        $this->middleware('sys_entry')->only(['store','update','destroy']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Fault::query();
            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('photo', function ($row) {
                    if(!$row->photo) return 'No image';
                    return '<img src="'.asset($row->photo).'" width="50" height="50"/>';
                })
                ->editColumn('video', function ($row) {
                    if(!$row->video) return 'No video';
                    return '<video width="50" height="50" controls><source src="'.asset($row->video).'" type="video/mp4"></video>';
                })
                ->editColumn('solved_by',function ($row){
                    return $row->solvedBy->name ?? 'No user';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="button-group" role="group">
                                <a class="btn btn-sm btn-primary" href="'.route('user.fault.edit',$row->id).'"><i class="fa fa-edit"></i></a>
                                <button class="btn btn-sm btn-danger btnDelete"  data-url="'.route('user.fault.destroy',$row->id).'"><i class="fa fa-x"></i></button>
                            </div>';
                })
                ->rawColumns(['photo','video','solved_by','action'])
                ->make(true);
        }
        return view('user.fault.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        return view('user.fault.create',compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FaultStoreRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();

            $file->storeAs('users/photos', $fileName, 'public');
            $data['photo'] = 'storage/users/photos/' . $fileName;
        }
        if ($request->hasFile('video')) {
            $file = $request->file('video');
            $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();

            $file->storeAs('users/videos', $fileName, 'public');
            $data['video'] = 'storage/users/videos/' . $fileName;
        }
        Fault::create($data);
        return response(['status' => true, 'message' => 'Fault added successfully','url'=>route('user.fault.index')]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Fault $fault)
    {
        return view('user.fault.show',compact('fault'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fault $fault)
    {
        $users = User::all();
        return view('user.fault.edit',compact('fault','users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FaultUpdateRequest $request, Fault $fault)
    {
        $data = $request->validated();
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();

            $file->storeAs('users/photos', $fileName, 'public');
            $data['photo'] = 'storage/users/photos/' . $fileName;
        }
        if ($request->hasFile('video')) {
            $file = $request->file('video');
            $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();

            $file->storeAs('users/videos', $fileName, 'public');
            $data['video'] = 'storage/users/videos/' . $fileName;
        }
        $fault->update($data);
        return response(['status' => true, 'message' => 'Fault updated successfully','url'=>route('user.fault.index')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fault $fault)
    {
        $fault->delete();
        return response(['status' => true, 'message' => 'Fault deleted successfully','url'=>route('user.fault.index')]);
    }
}
