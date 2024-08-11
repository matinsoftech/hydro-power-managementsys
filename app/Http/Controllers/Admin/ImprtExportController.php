<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExcelData;

class ImprtExportController extends Controller
{
    public function import_export(){
       return view('admin.import_export');
    }
    public function import_file(Request $request){
        $datas=ExcelData::all();
        dd($datas);
    //    return view('admin.import_export');
    }
    public function export_file(){
        return "hello";
    //    return view('admin.import_export');
    }
}
