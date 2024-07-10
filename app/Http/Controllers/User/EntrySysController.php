<?php

namespace App\Http\Controllers\User;

use App\Models\EntrySys;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EntrySysController extends Controller
{
    public function index()
    {
        $entrySystemsHistories = EntrySys::where('user_id', auth()->user()->id)->latest()->paginate(10);
        return response()->json([
            'success' => true,
            'data' => $entrySystemsHistories
        ]);
    }

    public function store()
    {
        $entrySysExists = EntrySys::where('date', now()->format('Y-m-d'))->latest()->first();

        if($entrySysExists) {

            if($entrySysExists->user_id != auth()->user()->id && $entrySysExists->check_out_time == null){

                return response()->json([
                    'success' => false,
                    'data' => 'Someone has already checked in please let user check out first.',
                    'message' => 'Someone has already checked in please let user check out first.'
                ]);

            }


            if($entrySysExists->check_out_time){
                $entrySysExists = EntrySys::create([
                    'date' => now()->format('Y-m-d'),
                    'check_in_time' => now()->format('H:i:s'),
                    'user_id' => auth()->user()->id
                ]);

                return response()->json([
                    'success' => true,
                    'data' => $entrySysExists,
                    'message' => 'Successfully checked in'
                ]);

            }else{
                $entrySysExists->update([
                    'check_out_time' => now()->format('H:i:s')
                ]);

                return response()->json([
                    'success' => true,
                    'data' => $entrySysExists,
                    'message' => 'Successfully checked out'
                ]);
            }

        }

        $entrySys = EntrySys::create([
            'date' => now()->format('Y-m-d'),
            'check_in_time' => now()->format('H:i:s'),
            'user_id' => auth()->user()->id
        ]);
        return response()->json([
            'success' => true,
            'data' => $entrySys,
            'message' => 'Successfully checked in'
        ]);
    }
}
