<?php

namespace App\Http\Middleware\DashBoard\User;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePhoneIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       if ($request->user() && is_null($request->user()->phone_verified_at)) {
            return response()->json([
                'status' => false,
                'message' => 'Your phone number is not verified yet.',
                'error_code' => 'PHONE_NOT_VERIFIED'
            ], 403);
        }
        return $next($request);
    }
}
