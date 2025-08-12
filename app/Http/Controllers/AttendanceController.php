<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Attendance;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function showTodayAttendance()
    {
        try {
            // Attendance
            $attendance = Attendance::select(
                'id',
                DB::raw('DATE(date) as date'),
                'type as status',
                'keterangan'
            )
                ->whereDate('date', Carbon::today())
                ->orderBy('date', 'asc')
                ->get();

            $absence = Absence::select(
                'id',
                DB::raw('DATE(`date-start`) as date'),
                'type as status',
                'description as keterangan'
            )
                ->whereDate('date-start', Carbon::today())
                ->orderByRaw('`date-start` asc')
                ->get();


            // Merge & sort
            $data = $attendance
                ->values() // reset index biar nggak ketimpa
                ->concat($absence->values()) // gabung tanpa replace key
                ->sortBy('date')
                ->values(); // reset index lagi setelah sort


            return response()->json([
                'status'  => 'success',
                'message' => 'Data found',
                'count'   => $data->count(),
                'data'    => $data
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Server error',
                'error'   => $e->getMessage()
            ]);
        }
    }

    /**
     * Absen Masuk
     */
    public function checkIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'type' => 'required|in:card,mobile',
            'longitude' => 'required|numeric',
            'nip' => 'nullable|string|max:20',
            'keterangan' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => "error",
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $type = $request->type;
            if (!in_array($type, ['card', 'mobile'])) {
                return response()->json([
                    'status' => "error",
                    'message' => 'Tipe absensi tidak valid'
                ], 400);
            }

            $userId = null;

            if ($type === 'card') {
                if (!$request->has('nip')) {
                    return response()->json([
                        'status' => "error",
                        'message' => 'NIP harus diisi untuk absensi dengan kartu'
                    ], 400);
                }

                $userId = User::where('nip', $request->nip)->value('id');
                if (!$userId) {
                    return response()->json([
                        'status' => "error",
                        'message' => 'User tidak ditemukan'
                    ], 400);
                }
            } else {
                if (!auth()->check()) {
                    return response()->json([
                        'status' => "error",
                        'message' => 'Unauthenticated.'
                    ], 401);
                }

                $userId = auth()->id();
            }

            $today = Carbon::today()->format('Y-m-d');
            $currentTime = Carbon::now();
            $dayName = strtolower($currentTime->format('l')); // monday, tuesday, etc.

            // Cek apakah sudah absen masuk hari ini
            $existingAttendance = Attendance::where('user_id', $userId)
                ->where('date', $today)
                ->whereNotNull('check_in_time')
                ->first();

            if ($existingAttendance) {
                return response()->json([
                    'status' => "error",
                    'message' => 'Anda sudah absen masuk hari ini'
                ], 400);
            }

            // Ambil jadwal kerja
            $workSchedule = Setting::first(); // Sesuaikan query sesuai kebutuhan

            if (!$workSchedule) {
                return response()->json([
                    'status' => "error",
                    'message' => 'Jadwal kerja belum diatur'
                ], 400);
            }

            // Validasi apakah hari ini adalah hari kerja
            $isActiveField = $dayName . '_is_active';
            $startTimeField = $dayName . '_start_time';
            $endTimeField = $dayName . '_end_time';

            if (!$workSchedule->$isActiveField) {
                return response()->json([
                    'status' => "error",
                    'message' => 'Hari ini bukan hari kerja'
                ], 400);
            }

            // Validasi lokasi
            $distance = $this->calculateDistance(
                $request->latitude,
                $request->longitude,
                $workSchedule->latitude,
                $workSchedule->longitude
            );

            if ($distance > $workSchedule->radius) {
                return response()->json([
                    'status' => "error",
                    'message' => 'Anda berada di luar radius lokasi kerja',
                    'data' => [
                        'distance' => round($distance, 2),
                        'max_radius' => $workSchedule->radius,
                        'location_name' => $workSchedule->location_name
                    ]
                ], 400);
            }

            // Tentukan status keterlambatan
            $startTime = Carbon::createFromFormat('H:i:s', $workSchedule->$startTimeField);
            $isLate = $currentTime->format('H:i:s') > $startTime->format('H:i:s');

            // Simpan absensi masuk
            $attendance = Attendance::create([
                'user_id' => $userId,
                'type' => $type,
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                'date' => $today,
                'check_in_time' => $currentTime,
                'keterangan' => $request->keterangan,
                'lokasi' => $workSchedule->location_name
            ]);

            return response()->json([
                'status' => "success",
                'message' => 'Absen masuk berhasil',
                'data' => [
                    'attendance_id' => $attendance->id,
                    'check_in_time' => $attendance->check_in_time,
                    'status' => $attendance->type,
                    'location' => $attendance->lokasi,
                    'is_late' => $isLate,
                    'distance' => round($distance, 2)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => "error",
                'message' => 'Terjadi kesalahan sistem',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function showAttendanceByPagination(Request $request)
    {
        try {
            // Attendance -> semua data
            $attendance = Attendance::select(
                'id',
                DB::raw('DATE(date) as date'),
                'type as status',
                'keterangan'
            )
                ->orderBy('date', 'desc')
                ->get();

            // Absence -> semua data
            $absence = Absence::select(
                'id',
                DB::raw('DATE(`date-start`) as date'),
                'type as status',
                'description as keterangan'
            )
                ->orderByRaw('DATE(`date-start`) desc')
                ->get();

            // Gabungkan kedua data dengan concat()
            $data = $attendance->concat($absence)
                ->sortByDesc('date') // Urutkan desc (terbaru di atas)
                ->values();

            // Pagination manual
            $perPage = 10;
            $page = request()->get('page', 1);
            $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
                $data->forPage($page, $perPage),
                $data->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );

            return response()->json([
                'status'  => 'success',
                'message' => 'Data found',
                'count'   => $paginated->total(),
                'data'    => $paginated->items(),
                'pagination' => [
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                    'per_page' => $paginated->perPage(),
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Server error',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Absen Pulang
     */
    public function checkOut(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'type' => 'required|in:card,mobile',
            'nip' => 'nullable|string|max:20',
            'keterangan' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => "error",
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $type = $request->type;
            if (!in_array($type, ['card', 'mobile'])) {
                return response()->json([
                    'status' => "error",
                    'message' => 'Tipe absensi tidak valid'
                ], 400);
            }

            $userId = null;

            if ($type === 'card') {
                if (!$request->has('nip')) {
                    return response()->json([
                        'status' => "error",
                        'message' => 'NIP harus diisi untuk absensi dengan kartu'
                    ], 400);
                }

                $userId = User::where('nip', $request->nip)->value('id');
                if (!$userId) {
                    return response()->json([
                        'status' => "error",
                        'message' => 'User tidak ditemukan'
                    ], 400);
                }
            } else {
                // check login session
                if (!auth()->check()) {
                    return response()->json([
                        'status' => "error",
                        'message' => 'Unauthenticated.'
                    ], 401);
                }

                $userId = auth()->id();
            }

            $today = Carbon::today()->format('Y-m-d');
            $currentTime = Carbon::now();

            // Cari data absensi masuk hari ini
            $attendance = Attendance::where('user_id', $userId)
                ->where('date', $today)
                ->whereNotNull('check_in_time')
                ->first();

            if (!$attendance) {
                return response()->json([
                    'status' => "error",
                    'message' => 'Anda belum absen masuk hari ini'
                ], 400);
            }

            if ($attendance->check_out_time) {
                return response()->json([
                    'status' => "error",
                    'message' => 'Anda sudah absen pulang hari ini'
                ], 400);
            }

            // Ambil jadwal kerja
            $workSchedule = Setting::first();

            if (!$workSchedule) {
                return response()->json([
                    'status' => "error",
                    'message' => 'Jadwal kerja belum diatur'
                ], 400);
            }

            // Validasi lokasi
            $distance = $this->calculateDistance(
                $request->latitude,
                $request->longitude,
                $workSchedule->latitude,
                $workSchedule->longitude
            );

            if ($distance > $workSchedule->radius) {
                return response()->json([
                    'status' => "error",
                    'message' => 'Anda berada di luar radius lokasi kerja',
                    'data' => [
                        'distance' => round($distance, 2),
                        'max_radius' => $workSchedule->radius,
                        'location_name' => $workSchedule->location_name
                    ]
                ], 400);
            }

            // Update absensi pulang
            $attendance->update([
                'check_out_time' => $currentTime,
                'keterangan' => $request->keterangan ?: $attendance->keterangan
            ]);

            // Hitung total jam kerja
            $checkInTime = Carbon::parse($attendance->check_in_time);
            $checkOutTime = Carbon::parse($attendance->check_out_time);
            $workingHours = $checkOutTime->diffInHours($checkInTime);
            $workingMinutes = $checkOutTime->diffInMinutes($checkInTime) % 60;

            return response()->json([
                'status' => "success",
                'message' => 'Absen pulang berhasil',
                'data' => [
                    'attendance_id' => $attendance->id,
                    'check_in_time' => $attendance->check_in_time,
                    'check_out_time' => $attendance->check_out_time,
                    'working_hours' => $workingHours . ' jam ' . $workingMinutes . ' menit',
                    'total_minutes' => $checkOutTime->diffInMinutes($checkInTime),
                    'location' => $attendance->lokasi,
                    'distance' => round($distance, 2)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => "error",
                'message' => 'Terjadi kesalahan sistem',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Riwayat Absensi
     */
    // Option 1: Clean & Mobile-Friendly Response
    public function history(Request $request)
    {
        try {
            $userId = auth()->id();
            $limit = $request->get('limit', 10);
            $month = $request->get('month');
            $year = $request->get('year');

            $query = Attendance::orderBy('date', 'desc');

            if ($month && $year) {
                $query->whereMonth('date', $month)
                    ->whereYear('date', $year);
            }

            $attendances = $query->paginate($limit);

            // Format data untuk mobile
            $formattedData = $attendances->map(function ($attendance) {
                $workingHours = null;
                if ($attendance->check_in_time && $attendance->check_out_time) {
                    $checkIn = Carbon::parse($attendance->check_in_time);
                    $checkOut = Carbon::parse($attendance->check_out_time);
                    $totalMinutes = $checkOut->diffInMinutes($checkIn);
                    $workingHours = [
                        'hours' => intval($totalMinutes / 60),
                        'minutes' => $totalMinutes % 60,
                        'total_minutes' => $totalMinutes,
                        'formatted' => intval($totalMinutes / 60) . ' jam ' . ($totalMinutes % 60) . ' menit'
                    ];
                }

                return [
                    'id' => $attendance->id,
                    'date' => $attendance->date,
                    'day_name' => Carbon::parse($attendance->date)->format('l'),
                    'check_in_time' => $attendance->check_in_time ? Carbon::parse($attendance->check_in_time)->format('H:i:s') : null,
                    'check_out_time' => $attendance->check_out_time ? Carbon::parse($attendance->check_out_time)->format('H:i:s') : null,
                    'status' => $attendance->type,
                    'status_text' => $attendance->type == 'on_time' ? 'Tepat Waktu' : 'Terlambat',
                    'location' => $attendance->lokasi,
                    'keterangan' => $attendance->keterangan,
                    'working_hours' => $workingHours,
                    'is_complete' => !is_null($attendance->check_out_time)
                ];
            });

            return response()->json([
                'status' => "success",
                'message' => 'Data riwayat absensi',
                'data' => [
                    'records' => $formattedData,
                    'pagination' => [
                        'current_page' => $attendances->currentPage(),
                        'total_pages' => $attendances->lastPage(),
                        'per_page' => $attendances->perPage(),
                        'total_records' => $attendances->total(),
                        'has_more' => $attendances->hasMorePages()
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => "error",
                'message' => 'Terjadi kesalahan sistem',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    // Option 3: Just the Essential Data


    /**
     * Status Absensi Hari Ini
     */
    public function todayStatus()
    {
        try {
            $userId = auth()->id();
            $today = Carbon::today()->format('Y-m-d');

            $attendance = Attendance::where('user_id', $userId)
                ->where('date', $today)
                ->first();

            $workSchedule = Setting::first();
            $dayName = strtolower(Carbon::now()->format('l'));
            $isWorkingDay = $workSchedule ? $workSchedule->{$dayName . '_is_active'} : false;

            return response()->json([
                'status' => "success",
                'message' => 'Status absensi hari ini',
                'data' => [
                    'date' => $today,
                    'is_working_day' => $isWorkingDay,
                    'has_checked_in' => $attendance ? !is_null($attendance->check_in_time) : false,
                    'has_checked_out' => $attendance ? !is_null($attendance->check_out_time) : false,
                    'check_in_time' => $attendance ? $attendance->check_in_time : null,
                    'check_out_time' => $attendance ? $attendance->check_out_time : null,
                    'type' => $attendance ? $attendance->type : null,
                    'work_schedule' => $workSchedule ? [
                        'start_time' => $workSchedule->{$dayName . '_start_time'},
                        'end_time' => $workSchedule->{$dayName . '_end_time'},
                        'location_name' => $workSchedule->location_name
                    ] : null
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => "error",
                'message' => 'Terjadi kesalahan sistem',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hitung jarak antara dua koordinat (Haversine Formula)
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meter

        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLatRad = deg2rad($lat2 - $lat1);
        $deltaLonRad = deg2rad($lon2 - $lon1);

        $a = sin($deltaLatRad / 2) * sin($deltaLatRad / 2) +
            cos($lat1Rad) * cos($lat2Rad) *
            sin($deltaLonRad / 2) * sin($deltaLonRad / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // jarak dalam meter
    }
}
