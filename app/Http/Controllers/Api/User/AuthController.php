<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $guard;

    public function __construct()
    {
        $this->guard = 'api';
        auth()->shouldUse($this->guard);
    }



    public function login()
    {
        try {
            $credentials = request(['phone', 'password']);

            if (! $token =  auth($this->guard)->attempt($credentials)) {
                return response()->json(['message' => 'Invalid phone or password.'], 401);
            }

            return $this->respondWithToken($token);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Login failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function me()
    {
        return response()->json([
            'data' => auth($this->guard)->user(),
        ]);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */




    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth($this->guard)->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $newToken = auth($this->guard)->refresh();
        auth($this->guard)->setToken($newToken);

        return $this->respondWithToken($newToken);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth($this->guard)->factory()->getTTL() * 60,
            'user' => auth($this->guard)->user(),
        ]);
    }
}
