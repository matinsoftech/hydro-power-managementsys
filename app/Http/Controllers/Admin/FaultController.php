<?php

namespace App\Http\Controllers\Admin;

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
    public function index(Request $request)
    {
        if ($request->is('api/*')) {
            return $this->apiIndex($request);
        }

        if ($request->ajax()) {
            $query = Fault::query();

            // Apply date filter if both start and end dates are provided
            if ($request->has('start_date') && $request->has('end_date')) {
                $startDate = $request->input('start_date');
                $endDate = $request->input('end_date');

                // Check if both dates are valid and not empty
                if (!empty($startDate) && !empty($endDate)) {
                    $query->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59']);
                }
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('photo', function ($row) {
                    if (!$row->photo) return 'No image';
                    return '<img src="' . asset('FaultPhoto/' . $row->photo) . '" width="50" height="50"/>';
                })
                ->editColumn('video', function ($row) {
                    if (!$row->video) return 'No video';
                    return '<video width="50" height="50" controls><source src="' . asset('FaultVideo/' . $row->video) . '" type="video/mp4"></video>';
                })
                ->editColumn('solved_by', function ($row) {
                    return $row->solvedBy->name ?? 'No user';
                })
                ->editColumn('found_by', function ($row) {
                    return $row->foundBy->name ?? 'No user';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="button-group" role="group">
                                <a class="btn btn-sm btn-primary" href="' . route('admin.fault.edit', $row->id) . '"><i class="fa fa-edit"></i></a>
                                <button class="btn btn-sm btn-danger btnDelete" data-url="' . route('admin.fault.destroy', $row->id) . '"><i class="fa fa-x"></i></button>
                            </div>';
                })
                ->rawColumns(['photo', 'video', 'solved_by', 'action'])
                ->make(true);
        }

        return view('admin.fault.index');
    }

    public function apiIndex(Request $request)
    {
        $query = Fault::query();
        if($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }
        if($request->sort_by == "latest"){
            $query->latest();
        }
        // $data = $query->with('solvedBy')->paginate(20);
        // return response()->json(['data'=>$data]);

          // Load relationships as needed
    $query->with(['solvedBy', 'foundBy']); // Ensure 'foundBy' is a valid relationship

    // Paginate results
    $data = $query->paginate(20);

    // Return JSON response with paginated data
    return response()->json(['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        return view('admin.fault.create',compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // $request->validate([
            //     'photo' => 'nullable|image|max:2048', // Max size 2MB for images
            //     'video' => 'nullable|file|max:204800', // Max size 200MB for videos
            // ]);

            $datas = new Fault;
            $datas->fault_time = $request->fault_time;
            $datas->reason = $request->reason;

            // Photo upload
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                if ($photo->isValid()) {
                    $imagename = time() . '.' . $photo->extension();
                    $photo->move(public_path('FaultPhoto'), $imagename);
                    $datas->photo = $imagename;
                } else {
                    throw new \Exception('Photo upload failed.');
                }
            }

            // Video upload
            if ($request->hasFile('video')) {
                $video = $request->file('video');
                if ($video->isValid()) {
                    $videoname = time() . '.' . $video->extension();
                    $video->move(public_path('FaultVideo'), $videoname);
                    $datas->video = $videoname;
                } else {
                    throw new \Exception('Video upload failed.');
                }
            }

            $datas->status = $request->status;
            $datas->solved_by = $request->solved_by;
            $datas->found_by = $request->found_by;
            $datas->save();

            return redirect()->back()->with('success', 'Fault created successfully!');
        } catch (\Exception $e) {
            \Log::error('Error creating fault: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while creating the fault.');
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Fault $fault)
    {
        return view('admin.fault.show',compact('fault'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fault $fault)
    {

        $users = User::all();
        return view('admin.fault.edit',compact('fault','users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fault $fault)
{
    try {
        // Update fault attributes
        $fault->fault_time = $request->fault_time;
        $fault->reason = $request->reason;

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            if ($photo->isValid()) {
                $imagename = time() . '.' . $photo->extension();
                $photo->move(public_path('FaultPhoto'), $imagename);
                $fault->photo = $imagename;
            }
        }

        // Handle video upload
        if ($request->hasFile('video')) {
            $video = $request->file('video');
            if ($video->isValid()) {
                $videoname = time() . '.' . $video->extension();
                $video->move(public_path('FaultVideo'), $videoname);
                $fault->video = $videoname;
            }
        }

        // Update other attributes
        $fault->status = $request->status;
        $fault->solved_by = $request->solved_by;
        $fault->save();

        return redirect()->back()->with('success', 'Fault updated successfully!');
    } catch (\Exception $e) {
        \Log::error('Error updating fault: ' . $e->getMessage());
        return redirect()->back()->with('error', 'An error occurred while updating the fault.');
    }
}




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fault $fault)
    {
        // return "hello";
        $fault->delete();
        return response(['status' => true, 'message' => 'Fault deleted successfully','url'=>route('admin.fault.index')]);
    }
}
