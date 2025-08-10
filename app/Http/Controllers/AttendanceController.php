<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function indexHarian()
    {
        $today = Carbon::today();
        $dayName = strtolower($today->format('l')); // ex: monday, tuesday, etc

        // Ambil setting jam terlambat
        $setting = DB::table('settings')->first();
        $jamTerlambat = $setting->{$dayName . '_start_time'} ?? '08:00:00';

        // Total semua karyawan
        $totalKaryawan = DB::table('users')->count();

        // User hadir tepat waktu
        $totalHadir = DB::table('attendances')
            ->whereDate('date', $today)
            ->whereTime('time', '<=', $jamTerlambat)
            ->count();

        // User hadir terlambat
        $totalTerlambat = DB::table('attendances')
            ->whereDate('date', $today)
            ->whereTime('time', '>', $jamTerlambat)
            ->count();

        // Semua user yang hadir (tepat waktu + terlambat)
        $hadirHariIni = DB::table('attendances')
            ->whereDate('date', $today)
            ->pluck('user_id')
            ->toArray();

        // Semua user yang izin/sakit hari ini
        $absenHariIni = DB::table('absences')
            ->whereDate('date-start', '<=', $today)
            ->whereDate('date-end', '>=', $today)
            ->pluck('user_id')
            ->toArray();

        // Semua user yang tidak hadir TANPA izin
        $tidakHadirTanpaIzin = array_diff(
            DB::table('users')->pluck('id')->toArray(), // semua user
            $hadirHariIni, // yang hadir
            $absenHariIni  // yang izin
        );

        // Total tidak hadir = izin + tanpa izin
        $totalTidakHadir = count($absenHariIni) + count($tidakHadirTanpaIzin);

        return response()->json([
            'tanggal' => $today->toDateString(),
            'total_karyawan' => $totalKaryawan,
            'total_hadir' => $totalHadir,
            'total_terlambat' => $totalTerlambat,
            'total_tidak_hadir' => $totalTidakHadir
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        //
    }
}
