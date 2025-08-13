<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Admin;

class EnsureAdminAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        $auth = $request->user(); // requires auth:sanctum on route

        if (!$auth) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        if (!($auth instanceof Admin)) {
            return response()->json(['status' => 'error', 'message' => 'Forbidden: admin token required'], 403);
        }

        return $next($request);
    }
}
