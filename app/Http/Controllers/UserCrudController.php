<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\KaryawanImport;
use App\Models\Absence;
use App\Models\Attendance;
use App\Models\Setting;

class UserCrudController extends Controller
{
    // public function readAllUser()
    // {
    //     try {
    //         $data = User::orderBy('id', 'asc')->get()->makeVisible(['password', 'serialNumber']);
    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Data found', 'data' => $data
    //         ]);
    //     } catch (Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Server error', 'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function addUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'nip' => 'required|string|max:20|unique:users',
                'email' => ['required', 'email', 'max:255', Rule::unique('users')],
                'password' => 'nullable|string|min:6',
                'telepon' => 'nullable|string|max:15',
                'divisi' => 'nullable|string|max:100',
                'mapel' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()], 400);
            }

            $password = $request->filled('password') ? $request->password : $request->nip;

            $user = User::create([
                'name' => $request->name,
                'password' => Hash::make($password),
                'nip' => $request->nip,
                'email' => $request->email,
                'telepon' => $request->telepon,
                'divisi' => $request->divisi,
                'mapel' => $request->mapel,
            ]);

            return response()->json(['status' => true, 'message' => 'User created successfully', 'data' => $user], 201);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }

    public function readAllUser(Request $request)
    {
        try {
            $query = User::query();

            // Fitur pencarian
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%")
                        ->orWhere('nip', 'like', "%$search%")
                        ->orWhere('telepon', 'like', "%$search%")
                        ->orWhere('divisi', 'like', "%$search%")
                        ->orWhere('mapel', 'like', "%$search%");
                });
            }

            // Fitur sorting
            $sortField = $request->input('sort_field', 'id');
            $sortOrder = $request->input('sort_order', 'asc');

            // Validasi field sorting untuk mencegah SQL injection
            $validSortFields = ['id', 'name', 'username', 'email', 'nip', 'telepon', 'divisi', 'mapel', 'created_at', 'updated_at'];
            if (!in_array($sortField, $validSortFields)) {
                $sortField = 'id';
            }

            $sortOrder = strtolower($sortOrder) === 'desc' ? 'desc' : 'asc';

            $query->orderBy($sortField, $sortOrder);

            // Fitur pagination
            $perPage = $request->input('per_page', 10);
            $users = $query->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'Data found',
                'data' => $users->items(),
                'meta' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'sort_field' => $sortField,
                    'sort_order' => $sortOrder,
                    'search_query' => $request->search ?? null,
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function showUserById(?string $id = null)
    {
        try {
            if (is_null($id)) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'ID is required'
                ], 400);
            }

            $data = User::find($id);
            if ($data) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data found',
                    'data' => $data
                ]);
            }
            return response()->json([
                'status' => 'info',
                'message' => 'Data not found'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function showDetailWithAttendance(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Ambil data hadir
        $attendances = Attendance::where('user_id', $id)
            ->get()
            ->map(function ($item) {
                return [
                    'tanggal' => $item->date,
                    'waktu' => $item->check_in_time,
                    'metode-absen' => $item->type,
                    'lokasi' => $item->lokasi,
                    'status' => 'Hadir',
                    'keterangan' => $item->keterangan ?? null,
                ];
            });

        // Ambil data tidak hadir
        $absences = Absence::where('user_id', $id)
            ->get()
            ->map(function ($item) {
                return [
                    'tanggal' => $item->date_start,
                    'waktu' => '-',
                    'metode-absen' => $item->type,
                    'lokasi' => '-',
                    'status' => 'Tidak Hadir',
                    'keterangan' => $item->type ?? null,
                ];
            });

        // Gabung & urutkan berdasarkan tanggal terbaru
        $history = $attendances
            ->merge($absences)
            ->sortByDesc('tanggal')
            ->values();

        return response()->json([
            'status' => 'success',
            'message' => 'Detail data guru dan riwayat absensi berhasil diambil.',
            'data' => [
                'user' => $user,
                'riwayat_absensi' => $history
            ],
        ]);
    }

    public function updateUser(Request $request, ?string $id = null)
    {
        try {
            if (is_null($id)) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'ID is required'
                ], 400);
            }

            $userdata = User::find($id);
            if (!$userdata) return response()->json([
                'status' => false,
                'message' => 'Data not found'
            ], 404);

            $data = array_filter($request->only(['name', 'username', 'password', 'nip', 'email', 'telepon', 'divisi', 'mapel']), fn($value) => $value !== null);
            if (empty($data)) return response()->json([
                'status' => false,
                'message' => 'No data to update'
            ], 400);

            $validator = Validator::make($data, [
                'name' => 'nullable',
                'username' => ['nullable', 'string', Rule::unique('users', 'username')->ignore($id)],
                'password' => 'nullable|min:6',
                'nip' => 'nullable',
                'email' => ['nullable', 'email', Rule::unique('users', 'email')->ignore($id)],
                'telepon' => 'nullable',
                'divisi' => 'nullable',
                'mapel' => 'nullable',
            ]);

            if ($validator->fails()) return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);

            $userdata->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Success update data'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteUser(?string $id = null)
    {
        try {
            if (is_null($id)) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'ID is required'
                ], 400);
            }

            $userdata = User::find($id);
            if (!$userdata) return response()->json(['status' => false, 'message' => 'Data not found'], 404);

            $userdata->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Success delete data'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getMyCurrentActivity()
    {
        $user = auth()->user();

        $currentActivity = [
            'check_in_time' => null,
            'check_out_time' => null,
            'is_late' => false,
            'late_duration_minutes' => 0,
        ];

        $today = now()->toDateString();
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        $expectedCheckIn = null;
        if ($settings = Setting::first()) {
            $day = strtolower(now()->format('l'));
            $startField  = "{$day}_start_time";
            $activeField = "{$day}_is_active";

            $isActive = property_exists($settings, $activeField) ? (bool) $settings->$activeField : true;
            if ($isActive && isset($settings->$startField) && $settings->$startField) {
                $expectedCheckIn = $settings->$startField;
                if (strlen($expectedCheckIn) === 5) {
                    $expectedCheckIn .= ':00';
                }
            }
        }

        if ($attendance) {
            $currentActivity['check_in_time'] = $attendance->check_in_time;
            $currentActivity['check_out_time'] = $attendance->check_out_time;

            if ($attendance->check_in_time && $expectedCheckIn && $attendance->check_in_time > $expectedCheckIn) {
                $currentActivity['is_late'] = true;
                $lateMinutes = \Carbon\Carbon::parse($attendance->check_in_time)
                    ->diffInMinutes(\Carbon\Carbon::parse($expectedCheckIn));
                $currentActivity['late_duration_minutes'] = $lateMinutes;
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $currentActivity + ['expected_check_in_time' => $expectedCheckIn]
        ]);
    }

    public function getMyStatistik()
    {
        $user = auth()->user();

        // jumlah dia masuk kerja
        $totalMasuk = Attendance::where('user_id', $user->id)->where('keterangan', 'masuk')->count();

        // jumlah dia telat
        $totalTelat = Attendance::where('user_id', $user->id)->where('keterangan', 'telat')->count();

        // total tidak masuk
        $totalTidakMasuk = Absence::where('user_id', $user->id)->count();

        $totalIzin = Absence::where('user_id', $user->id)->where('type', 'izin')->count();

        $totalSakit = Absence::where('user_id', $user->id)->where('type', 'sakit')->count();

        return response()->json([
            'status' => 'success',
            'message' => 'Statistik berhasil diambil',
            'data' => [
                'persentase_kehadiran' => $totalMasuk > 0 ? ($totalMasuk / ($totalMasuk + $totalTidakMasuk)) * 100 : 0,
                'jumlah_tidak_masuk' => $totalTidakMasuk,
                'jumlah_masuk' => $totalMasuk,
                'jumlah_telat' => $totalTelat,
                'jumlah_izin' => $totalIzin,
                'jumlah_sakit' => $totalSakit,
            ]
        ]);
    }

    public function import(Request $request)
    {
        // Validasi file harus excel
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        // Proses import
        try {
            Excel::import(new KaryawanImport, $request->file('file'));

            return response()->json([
                'status' => 'success',
                'message' => 'Data karyawan berhasil diimport.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengimport data: ' . $e->getMessage()
            ], 500);
        }
    }
}
