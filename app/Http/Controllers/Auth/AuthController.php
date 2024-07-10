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

    public function attemptApiLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (auth()->attempt($credentials)) {

            $user = User::find(Auth::user()->id);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(['success' => true,'token' => $token , 'user' => $user]);
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

    public function logout(Request $request){
        Auth::logout();
        if ($request->expectsJson()) {
            return response()->json(['status' => true,'message' => 'Logout successfully'], 200);
        }
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
            'confirm_password' => 'required_with:password|same:password',
            'avatar' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = User::find(Auth::user()->id);

        // Use the UploadImageTrait to get image paths
        $imagePaths = $this->uploadImage($request, ['avatar'], 'users');

        // Update the user model with the request data and image paths
        $user->update(array_merge($request->except('avatar'), $imagePaths));

        if (!empty($imagePaths)) {
            $user->imageGallery()->createMany([
                ['image_path' => $imagePaths['avatar']],
            ]);
        }

        return response()->json(['success' => true,'message' => 'Profile updated successfully','user' => $user]);
    }

}
