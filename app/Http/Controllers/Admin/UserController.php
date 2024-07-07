<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use PDO;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = User::query();
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<div class="button-group" role="group">
                                <a class="btn btn-sm btn-primary" href="'.route('admin.user.edit',$row->id).'"><i class="fa fa-edit"></i></a>
                                <button class="btn btn-sm btn-danger btnDelete" data-url="'.route('admin.user.destroy',$row->id).'"><i class="fa fa-x"></i></button>
                            </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.user.index');
    }

    public function create()
    {
        return view('admin.user.create');
    }

    public function store(UserStoreRequest $request)
    {
        User::create($request->validated());
        return response(['status' => true, 'message' => 'User added successfully','url'=>route('admin.user.index')]);
    }

    public function show(User $user)
    {
        return view('admin.user.show',compact('user'));
    }

    public function edit(User $user){

        return view('admin.user.edit',compact('user'));

    }

    public function update(UserUpdateRequest $request, User $user){
        $user->update($request->validated());
        return response(['status' => true, 'message' => 'User updated successfully','url'=>route('admin.user.index')]);
    }

    public function destroy(User $user){
        if($user->user_type == 'Admin'){
            return response(['status' => false, 'message' => 'Admin user can not be deleted']);
        }
        $user->delete();
        return response(['status' => true, 'message' => 'User Deleted Successfully','url'=>route('admin.user.index')]);
    }
}
