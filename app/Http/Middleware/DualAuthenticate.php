<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\User;
use App\Models\Admin;

class DualAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken() ?? $request->cookie('auth_token');

        if (!$token) {
            return response()->json(['status' => 'error', 'message' => 'Token tidak ditemukan', 'data' => $token], 401);
        }

        // Find the token in personal_access_tokens table
        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return response()->json(['status' => 'error', 'message' => 'Token tidak valid', 'data' => $token], 401);
        }

        // Get the tokenable model (User or Admin)
        $user = $accessToken->tokenable;

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 401);
        }

        // Set the authenticated user for the request
        if ($user instanceof User) {
            auth()->guard('sanctum')->setUser($user);
            $request->setUserResolver(function () use ($user) {
                return $user;
            });
        } elseif ($user instanceof Admin) {
            auth()->guard('admin-sanctum')->setUser($user);
            $request->setUserResolver(function () use ($user) {
                return $user;
            });
        } else {
            return response()->json(['status' => 'error', 'message' => 'Invalid user type'], 401);
        }

        return $next($request);
    }
}
