<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    public function index(): JsonResponse
    {
        $setting = Setting::first();

        if (!$setting) {
            $setting = Setting::create([]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Settings retrieved successfully',
            'data' => $setting
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            // Monday validation
            'monday_start_time' => 'required|date_format:H:i',
            'monday_end_time' => 'required|date_format:H:i',
            'monday_is_active' => 'required|boolean',

            // Tuesday validation
            'tuesday_start_time' => 'required|date_format:H:i',
            'tuesday_end_time' => 'required|date_format:H:i',
            'tuesday_is_active' => 'required|boolean',

            // Wednesday validation
            'wednesday_start_time' => 'required|date_format:H:i',
            'wednesday_end_time' => 'required|date_format:H:i',
            'wednesday_is_active' => 'required|boolean',

            // Thursday validation
            'thursday_start_time' => 'required|date_format:H:i',
            'thursday_end_time' => 'required|date_format:H:i',
            'thursday_is_active' => 'required|boolean',

            // Friday validation
            'friday_start_time' => 'required|date_format:H:i',
            'friday_end_time' => 'required|date_format:H:i',
            'friday_is_active' => 'required|boolean',

            // Saturday validation
            'saturday_start_time' => 'required|date_format:H:i',
            'saturday_end_time' => 'required|date_format:H:i',
            'saturday_is_active' => 'required|boolean',

            // Location validation
            'location_name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:10'
        ]);

        $setting = Setting::first();

        if ($setting) {
            $setting->update($request->all());
        } else {
            $setting = Setting::create($request->all());
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Settings saved successfully',
            'data' => $setting
        ]);
    }
}
