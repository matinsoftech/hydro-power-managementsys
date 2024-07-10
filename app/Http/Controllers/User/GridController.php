<?php

namespace App\Http\Controllers\User;

use App\Models\Grid;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Http\Requests\Grid\GridStoreRequest;
use App\Http\Requests\Grid\GridUpdateRequest;

class GridController extends Controller
{
    public function __construct()
    {
        $this->middleware('sys_entry')->only(['store','update','destroy']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Grid::query();
            return DataTables::of($query->with('createdBy'))
                ->addIndexColumn()
                ->editColumn('created_by',function ($row){
                    return $row->createdBy->name ?? 'No user';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="button-group" role="group">
                                <a class="btn btn-sm btn-primary" href="'.route('user.grid.edit',$row->id).'"><i class="fa fa-edit"></i></a>
                                <button class="btn btn-sm btn-danger btnDelete"  data-url="'.route('user.grid.destroy',$row->id).'"><i class="fa fa-x"></i></button>
                            </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('user.grid.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user.grid.create');
    }

    protected function prepareData(array $validatedData)
    {
        return array_merge($validatedData, [
            'created_by' => auth()->id(),
            'date' => now()->toDateString(),
        ]);
    }

    public function store(GridStoreRequest $request)
    {
        $data = $this->prepareData($request->validated());

        Grid::create($data);
        return response(['status' => true, 'message' => 'Grid added successfully','url'=>route('user.grid.index')]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Grid $grid)
    {
        return view('user.grid.show',compact('grid'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Grid $grid)
    {
        return view('user.grid.edit',compact('grid'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GridUpdateRequest $request, Grid $grid)
    {
        $data = $this->prepareData($request->validated());
        $grid->update($data);
        return response(['status' => true, 'message' => 'Grid updated successfully','url'=>route('user.grid.index')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Grid $grid)
    {
        $grid->delete();
        return response(['status' => true, 'message' => 'Grid deleted successfully','url'=>route('user.grid.index')]);
    }
}
