<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Lockout;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Hash;


use Exception;

class UserAuthController extends Controller
{

    public function login(Request $request)
    {
        try {
            $this->ensureIsNotRateLimited($request);

            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()], 400);
            }

            $user = User::where('email', $request->email)->first();


            if (!$user) {
                RateLimiter::hit($this->throttleKey($request));
                return response()->json(['status' => false, 'message' => 'Email tidak ditemukan'], 404);
            }

            if (!Hash::check($request->password, $user->password)) {
                RateLimiter::hit($this->throttleKey($request));
                return response()->json(['status' => false, 'message' => 'Password salah'], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            $serialNumber = Uuid::uuid4()->toString();

            RateLimiter::clear($this->throttleKey($request));

            return response()->json([
                'status' => 'success',
                'message' => 'Login sukses',
                'token' => $token,
                'serial_number' => $serialNumber,
                'user' => $user,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Terlalu banyak percobaan login. Coba lagi nanti.',
                'errors' => $e->errors(),
            ], 429);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    private function ensureIsNotRateLimited(Request $request): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    private function throttleKey(Request $request): string
    {
        return Str::lower($request->input('email')) . '|' . $request->ip();
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'username' => 'required',
                'password' => 'required|min:6',
                'nip' => 'required',
                'email' => 'required',
                'telepon' => 'required',
                'divisi' => 'required',
                'mapel' => 'required',
                
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal menyimpan data', 'data' => $validator->errors()], 400);
            }

            if (User::where('username', $request->username)->exists()) {
                return response()->json([
                    'status' => 'waring',
                    'message' => 'Username sudah digunakan'], 400);
            }

            $userdata = new User;
            $userdata->name = $request->name;
            $userdata->username = $request->username;
            $userdata->password = Hash::make($request->password);
            $userdata->nip = $request->nip;
            $userdata->email = $request->email;
            $userdata->telepon = $request->telepon;
            $userdata->divisi = $request->divisi;
            $userdata->mapel = $request->mapel;
            $userdata->save();

            return response()->json([
                'status' => 'success',
                 'message' => 'Data berhasil disimpan']);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }

    public function logout(Request $request)
{
    try {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logout berhasil'
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Gagal logout',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
