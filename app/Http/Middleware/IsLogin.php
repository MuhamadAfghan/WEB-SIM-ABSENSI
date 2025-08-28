<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->cookie('auth_token');
        if (!$token) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        } else {
            try {
                $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
                if (!$accessToken || !$accessToken->tokenable) {
                    return redirect()->route('login')->with('error', 'Token tidak valid');
                }

                $user = $accessToken->tokenable;

                // Support both User and Admin models
                if ($user instanceof \App\Models\User) {
                    auth()->guard('sanctum')->setUser($user);
                } elseif ($user instanceof \App\Models\Admin) {
                    auth()->guard('admin-sanctum')->setUser($user);
                } else {
                    return redirect()->route('login')->with('error', 'User type tidak valid');
                }

                return $next($request);
            } catch (\Exception $e) {
                return redirect()->route('login')->with('error', 'Token tidak valid');
            }
        }
    }
}
