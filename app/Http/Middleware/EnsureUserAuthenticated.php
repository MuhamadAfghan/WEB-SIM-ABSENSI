<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class EnsureUserAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        $auth = $request->user(); // requires auth:sanctum on route

        if (!$auth) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated'], 401);
        }

        if (!($auth instanceof User)) {
            return response()->json(['status' => false, 'message' => 'Forbidden: user token required'], 403);
        }

        return $next($request);
    }
}
