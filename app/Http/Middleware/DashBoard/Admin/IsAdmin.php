<?php

namespace App\Http\Middleware\DashBoard\Admin;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('admin-web')->check() || Auth::guard('admin-api')->check()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Access denied.'
            ], 401); // Unauthorized
        }



        return redirect()->route("login.index")->withErrors('Access denied.');
    }
}
