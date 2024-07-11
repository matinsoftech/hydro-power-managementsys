<?php

namespace App\Http\Controllers\Admin;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\UploadImageTrait;

class SiteSettingController extends Controller
{
    use UploadImageTrait;

    public function index()
    {
        $siteSetting = SiteSetting::first();
        return view('admin.site_setting.index',compact('siteSetting'));
    }

    public function update(Request $request,SiteSetting $siteSetting){
        $request->validate([
            'name' => 'required',
            'logo' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'email' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'description' => 'required',
            'keywords' => 'required',
            'latitude' =>  'required',
            'longitude' => 'required',

        ]);
        $imagePaths = $this->uploadImage($request, ['logo'], 'users');
        $siteSetting->update(array_merge($request->except('logo'), $imagePaths));
        return response()->json(['success'=>true,'message'=>'Site Setting Updated Successfully']);
    }
}
