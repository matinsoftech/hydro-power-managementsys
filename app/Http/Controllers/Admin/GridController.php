<?php

namespace App\Http\Controllers\Admin;

use App\Models\Grid;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Http\Requests\Grid\GridStoreRequest;
use App\Http\Requests\Grid\GridUpdateRequest;

class GridController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Grid::query();
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
                                <a class="btn btn-sm btn-primary" href="'.route('admin.grid.edit',$row->id).'"><i class="fa fa-edit"></i></a>
                                <button class="btn btn-sm btn-danger btnDelete"  data-url="'.route('admin.grid.destroy',$row->id).'"><i class="fa fa-x"></i></button>
                            </div>';
                })
                ->rawColumns(['photo','video','solved_by','action'])
                ->make(true);
        }
        return view('admin.grid.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        return view('admin.grid.create',compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GridStoreRequest $request)
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
        Grid::create($data);
        return response(['status' => true, 'message' => 'Grid added successfully','url'=>route('admin.grid.index')]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Grid $grid)
    {
        return view('admin.grid.show',compact('grid'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Grid $grid)
    {
        $users = User::all();
        return view('admin.grid.edit',compact('grid','users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GridUpdateRequest $request, Grid $grid)
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
        $grid->update($data);
        return response(['status' => true, 'message' => 'Grid updated successfully','url'=>route('admin.grid.index')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Grid $grid)
    {
        $grid->delete();
        return response(['status' => true, 'message' => 'Grid deleted successfully','url'=>route('admin.grid.index')]);
    }
}
