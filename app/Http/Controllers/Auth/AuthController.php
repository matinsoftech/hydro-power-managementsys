<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\UploadImageTrait;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use UploadImageTrait;

    public function login()
    {
        return view('auth.login');
    }

    public function attemptLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (auth()->attempt($credentials)) {
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false,'message' => 'Invalid email or password']);
    }

    public function register()
    {
        return view('auth.register');
    }

    public function attemptRegister(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create($request->all());

        Auth::login($user);

        return response()->json(['success' => true]);
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('login');
    }

    public function profile()
    {
        return view('auth.profile');
    }

    public function updateProfile(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'sometimes|string|min:6',
            'confirm_password' => 'sometimes|same:password',
        ]);

        $user = User::find(Auth::user()->id);

        $user->update($request->all());

        return response()->json(['success' => true,'message' => 'Profile updated successfully']);
    }

}
