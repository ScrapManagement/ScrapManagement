<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DashBoard\Admin\AdminRequest;
use App\Models\Admin\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    protected $guard;

    public function __construct()
    {
        $this->guard = 'admin-api';

        auth()->shouldUse($this->guard);
    }


    /**
     * Admin Login
     */
    public function login()
    {
        try {
            $credentials =request(['email', 'password']);

            if (! $token = auth($this->guard)->attempt($credentials)) {
                return response()->json(['message' => 'Invalid email or password.'], 401);
            }

            return $this->respondWithToken($token);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Login failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Logged Admin
     */
    public function me()
    {
        return response()->json([
            'data' => auth($this->guard)->user(),
        ]);
    }

    /**
     * Update Profile
     */


    /**
     * Logout (Invalidate Token)
     */
    public function logout()
    {
        auth($this->guard)->logout();

        return response()->json([
            'message' => 'Admin logged out successfully'
        ]);
    }

    /**
     * Refresh Token
     */
    public function refresh()
    {
        $newToken = auth($this->guard)->refresh();
        auth($this->guard)->setToken($newToken);

        return $this->respondWithToken($newToken);
    }

    /**
     * Token Response Structure
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth($this->guard)->factory()->getTTL() * 60,
            'admin'        => auth($this->guard)->user(),
        ]);
    }
}
