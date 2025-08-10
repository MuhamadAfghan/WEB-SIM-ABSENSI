<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function checkIn(Request $request)
    {
        $userId = $request->user_id;
        $today = Carbon::today();
        $dayname = strtolower(Carbon::now()->format('l'));

        $setting = Setting::first();

        $isActiveField = "{$dayname}_is_active";
        $startField = "{$dayname}_start_time";
        $endField = "{$dayname}_end_time";

        // check if the attendance is active for today
        if(!$setting->$isActiveField){
            return response()->json(
            [
            'message' => 'waktu absen tidak aktif',
            'status' => 'error'
            
            ], 400);
        }        

        $nowTime = Carbon::now()->format('H:i:s');

        // Simpan jam check in apa adanya
        $attendance = Attendance::where('user_id', $userId)
            ->where('date', $today)
            ->first();

        if ($attendance && $attendance->check_in_time) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sudah absen masuk hari ini'
            ], 400);
        }

        if (!$attendance) {
            $attendance = new Attendance();
            $attendance->user_id = $userId;
            $attendance->date = $today;
        }

        $attendance->check_in_time = $nowTime;
        $attendance->save();
 

        return response()->json([
            'message' => 'Check-in successful.',
            'status' => 'success',
            'data' => $attendance
        ]);

        
    }
}
