<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{
   // Register API (POST, formdata)
   public function register(Request $request){

    // data validation
    $request->validate([
        "name" => "required",
        "email" => "required|email|unique:users",
        "password" => "required|confirmed"
    ]);

    // Author model
    User::create([
        "name" => $request->name,
        "email" => $request->email,
        "password" => Hash::make($request->password)
    ]);

    // Response
    return response()->json([
        "status" => true,
        "message" => "User created successfully"
    ]);
}

// Login API (POST, formdata)
public function login(Request $request){

    // Data validation
    $request->validate([
        "email" => "required|email",
        "password" => "required"
    ]);

    // Auth Facade
    if(Auth::attempt([
        "email" => $request->email,
        "password" => $request->password
    ])){

        $user = Auth::user();

        $token = $user->createToken("myToken")->accessToken;

        return response()->json([
            "status" => true,
            "message" => "Login successful",
            "access_token" => $token
        ]);
    }

    return response()->json([
        "status" => false,
        "message" => "Invalid credentials"
    ]);
}

// Profile API (GET)
public function profile(){
    
    $userdata = Auth::user();

    return response()->json([
        "status" => true,
        "message" => "Profile data",
        "data" => $userdata
    ]);
}

// Logout API (GET)
public function logout(){

    auth()->user()->token()->revoke();

    return response()->json([
        "status" => true,
        "message" => "User logged out"
    ]);
}
}
