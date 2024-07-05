<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailConfig;
use Illuminate\Http\Request;

class MailSettingController extends Controller
{
    public function index(){
        $mailSetting = MailConfig::first();
        return view('admin.site_setting.mail_setting',compact('mailSetting'));
    }

    public function update(Request $request, MailConfig $mailSetting){
        $request->validate([
            'driver' => 'required',
            'host' => 'required',
            'port' => 'required',
            'username' => 'required',
            'password' => 'required',
            'encryption' => 'required',
            'from_address' => 'required',
            'from_name' => 'required',
        ]);
        $mailSetting->update($request->all());
        return response()->json(['success' => true,'message' => 'Mail Setting Updated Successfully']);
    }
}
