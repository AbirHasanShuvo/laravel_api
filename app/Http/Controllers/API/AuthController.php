<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ==============================
    // REGISTER
    // ==============================
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'errors' => $validator->errors()
            ], 400);
        }

        // Creating user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // hash manually
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'New user created successfully!',
            'user' => $user
        ], 201);
    }


    // ==============================
    // LOGIN
    // ==============================
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'errors' => $validator->errors()
            ], 400);
        }

        if (!Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid email or password'
            ], 401);
        }

        $user = Auth::user();

        // Sanctum token
        $token = $user->createToken('BlogApp')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ], 200);
    }

    //profile function

    public function profile()
    {
        $user = Auth::user();

        return response()->json([
            'status' => 'Success',
            'data' => $user

        ]);
    }
}
