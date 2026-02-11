<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(UserRegisterRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'user',
                'status' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'user' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Login user and return JWT token.
     */
    public function login(UserLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        // Check if user exists and is active
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        if (!$user->status) {
            return response()->json([
                'success' => false,
                'message' => 'Your account is blocked. Please contact administrator.',
            ], 403);
        }

        // Only allow regular users to login via this endpoint
        if ($user->role !== 'user') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Please use the admin login endpoint for administrative accounts.',
            ], 403);
        }

        // Attempt to authenticate
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ], 200);
    }

    /**
     * Login for admin panel only
     */
    public function adminLogin(UserLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        // Check if user exists and is active
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        if (!$user->status) {
            return response()->json([
                'success' => false,
                'message' => 'Your account is blocked. Please contact administrator.',
            ], 403);
        }

        // Ensure user is admin
        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Admin credentials required.',
            ], 403);
        }

        // Attempt to authenticate
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ], 200);
    }

    /**
     * Get authenticated user.
     */
    public function me()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            return response()->json([
                'success' => true,
                'user' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
    }

    /**
     * Logout user.
     */
    public function logout()
    {
        try {
            JWTAuth::parseToken()->invalidate();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
            ], 400);
        }
    }
}
