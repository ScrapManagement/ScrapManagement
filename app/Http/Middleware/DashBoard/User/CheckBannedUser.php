<?php

namespace App\Http\Middleware\DashBoard\User;

use Closure;
use Illuminate\Http\Request;

class CheckBannedUser
{
    public function handle(Request $request, Closure $next)
    {
        if (auth('api')->check() && auth('api')->user()->is_banned) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your account has been banned.',
                'ban_reason' => auth('api')->user()->ban_reason
            ], 403);
        }

        return $next($request);
    }
}

