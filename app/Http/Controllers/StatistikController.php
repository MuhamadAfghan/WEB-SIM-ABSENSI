<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class StatistikController extends Controller
{
    public function dashboardStatistik()
    {
        try {
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
                ->whereTime('check_in_time', '<=', $jamTerlambat)
                ->count();

            // User hadir terlambat
            $totalTerlambat = DB::table('attendances')
                ->whereDate('date', $today)
                ->whereTime('check_in_time', '>', $jamTerlambat)
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
                'status' => 'success',
                'message' => 'Statistik harian berhasil diambil',
                'data' => [
                    'tanggal' => $today->toDateString(),
                    'total_karyawan' => $totalKaryawan,
                    'total_hadir' => $totalHadir,
                    'total_terlambat' => $totalTerlambat,
                    'total_tidak_hadir' => $totalTidakHadir
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function statistikTahunan(Request $request)
    {
        try {
            // Validasi input tahun
            $validator = Validator::make($request->all(), [
                'year' => ['required', 'digits:4', 'numeric', 'min:1900', 'max:' . date('Y')],
            ], [
                'year.required' => 'Tahun wajib diisi.',
                'year.digits' => 'Tahun harus terdiri dari 4 digit.',
                'year.numeric' => 'Tahun harus berupa angka.',
                'year.min' => 'Tahun terlalu kecil.',
                'year.max' => 'Tahun tidak boleh lebih dari tahun sekarang.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $tahun = (int) $request->input('year');
            $data = [];
            $userIds = DB::table('users')->pluck('id')->toArray();

            // Siapkan struktur data 12 bulan
            for ($i = 1; $i <= 12; $i++) {
                $namaBulan = Carbon::createFromDate($tahun, $i, 1)->locale('id')->translatedFormat('F');
                $data[$i] = [
                    'bulan' => $namaBulan,
                    'total_karyawan' => count($userIds),
                    'total_hadir' => 0,
                    'total_terlambat' => 0,
                    'total_tidak_hadir' => 0,
                ];
            }

            // Ambil setting jam terlambat per hari
            $setting = DB::table('settings')->first();

            // Loop tiap bulan
            foreach ($data as $bulanNum => &$bulanData) {
                // Ambil semua tanggal di bulan ini
                $datesInMonth = [];
                $startDate = Carbon::create($tahun, $bulanNum, 1);
                $endDate = $startDate->copy()->endOfMonth();
                for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
                    $datesInMonth[] = $date->toDateString();
                }

                $hadirIds = [];
                $terlambatIds = [];
                $tidakHadirIds = [];

                foreach ($datesInMonth as $tgl) {
                    $dayName = strtolower(Carbon::parse($tgl)->format('l'));
                    $jamTerlambat = $setting->{$dayName . '_start_time'} ?? '08:00:00';

                    // Hadir tepat waktu
                    $hadirHariIni = DB::table('attendances')
                        ->whereDate('date', $tgl)
                        ->whereTime('check_in_time', '<=', $jamTerlambat)
                        ->pluck('user_id')
                        ->toArray();

                    // Hadir terlambat
                    $terlambatHariIni = DB::table('attendances')
                        ->whereDate('date', $tgl)
                        ->whereTime('check_in_time', '>', $jamTerlambat)
                        ->pluck('user_id')
                        ->toArray();

                    // Izin/sakit
                    $absenHariIni = DB::table('absences')
                        ->whereDate('date-start', '<=', $tgl)
                        ->whereDate('date-end', '>=', $tgl)
                        ->pluck('user_id')
                        ->toArray();

                    // Tidak hadir tanpa izin
                    $tidakHadirTanpaIzin = array_diff($userIds, $hadirHariIni, $terlambatHariIni, $absenHariIni);

                    $hadirIds = array_merge($hadirIds, $hadirHariIni);
                    $terlambatIds = array_merge($terlambatIds, $terlambatHariIni);
                    $tidakHadirIds = array_merge($tidakHadirIds, $tidakHadirTanpaIzin);
                }

                // Hitung unik user tiap kategori per bulan
                $bulanData['total_hadir'] = count(array_unique($hadirIds));
                $bulanData['total_terlambat'] = count(array_unique($terlambatIds));
                $bulanData['total_tidak_hadir'] = count(array_unique($tidakHadirIds));
            }

            // Rekap tahunan
            $rekapTahunan = [
                'total_karyawan' => count($userIds),
                'total_hadir' => 0,
                'total_terlambat' => 0,
                'total_tidak_hadir' => 0,
            ];
            foreach ($data as $bulan) {
                $rekapTahunan['total_hadir'] += $bulan['total_hadir'];
                $rekapTahunan['total_terlambat'] += $bulan['total_terlambat'];
                $rekapTahunan['total_tidak_hadir'] += $bulan['total_tidak_hadir'];
            }

            return response()->json([
                'status' => 'success',
                "message" => "Data Statistik Tahunan",
                "data" => [
                    'tahun' => $tahun,
                    'total_statistik_tahunan' => $rekapTahunan,
                    'data_bulanan' => array_values($data)
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function statistikBulanan(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'month' => 'required|string',
                'year' => 'nullable|numeric|digits:4|min:2020|max:' . date('Y'),
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $months = [
                'januari' => 1,
                'februari' => 2,
                'maret' => 3,
                'april' => 4,
                'mei' => 5,
                'juni' => 6,
                'juli' => 7,
                'agustus' => 8,
                'september' => 9,
                'oktober' => 10,
                'november' => 11,
                'desember' => 12,
                'january' => 1,
                'february' => 2,
                'march' => 3,
                'april' => 4,
                'may' => 5,
                'june' => 6,
                'july' => 7,
                'august' => 8,
                'september' => 9,
                'october' => 10,
                'november' => 11,
                'december' => 12,
            ];

            $monthName = strtolower($request->input('month'));
            $month = $months[$monthName] ?? null;

            if (!$month) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Nama bulan tidak valid',
                ], 422);
            }

            $year = (int) ($request->input('year') ?? date('Y'));

            // Ambil setting jam terlambat
            $setting = DB::table('settings')->first();

            // Ambil semua user
            $userIds = DB::table('users')->pluck('id')->toArray();

            // Ambil semua tanggal di bulan ini
            $startDate = Carbon::create($year, $month, 1);
            $endDate = $startDate->copy()->endOfMonth();

            $hadirIds = [];
            $terlambatIds = [];
            $sakitIds = [];
            $izinIds = [];

            for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
                $tgl = $date->toDateString();
                $dayName = strtolower($date->format('l'));
                $jamTerlambat = $setting->{$dayName . '_start_time'} ?? '08:00:00';

                // Hadir tepat waktu
                $hadirHariIni = DB::table('attendances')
                    ->whereDate('date', $tgl)
                    ->whereTime('check_in_time', '<=', $jamTerlambat)
                    ->pluck('user_id')
                    ->toArray();

                // Hadir terlambat
                $terlambatHariIni = DB::table('attendances')
                    ->whereDate('date', $tgl)
                    ->whereTime('check_in_time', '>', $jamTerlambat)
                    ->pluck('user_id')
                    ->toArray();

                // Sakit
                $sakitHariIni = DB::table('absences')
                    ->where('type', 'sakit')
                    ->whereDate('date-start', '<=', $tgl)
                    ->whereDate('date-end', '>=', $tgl)
                    ->pluck('user_id')
                    ->toArray();

                // Izin
                $izinHariIni = DB::table('absences')
                    ->where('type', 'izin')
                    ->whereDate('date-start', '<=', $tgl)
                    ->whereDate('date-end', '>=', $tgl)
                    ->pluck('user_id')
                    ->toArray();

                $hadirIds = array_merge($hadirIds, $hadirHariIni);
                $terlambatIds = array_merge($terlambatIds, $terlambatHariIni);
                $sakitIds = array_merge($sakitIds, $sakitHariIni);
                $izinIds = array_merge($izinIds, $izinHariIni);
            }

            // Hitung unik user tiap kategori
            $totalHadir = count(array_unique($hadirIds));
            $totalTerlambat = count(array_unique($terlambatIds));
            $totalSakit = count(array_unique($sakitIds));
            $totalIzin = count(array_unique($izinIds));

            // Tanpa keterangan (tidak hadir, tidak sakit, tidak izin)
            $allActiveUsers = array_unique(array_merge($hadirIds, $terlambatIds, $sakitIds, $izinIds));
            $totalTanpaKeterangan = count($userIds) - count($allActiveUsers);

            // Nama bulan Indonesia
            $indonesianMonth = Carbon::createFromDate($year, $month, 1)
                ->locale('id')
                ->translatedFormat('F');

            return response()->json([
                'status' => 'success',
                'message' => 'Data Statistik Bulanan',
                'data' => [
                    'bulan' => strtolower($indonesianMonth),
                    'tahun' => $year,
                    'total_karyawan' => count($userIds),
                    'total_hadir' => $totalHadir,
                    'total_terlambat' => $totalTerlambat,
                    'total_sakit' => $totalSakit,
                    'total_izin' => $totalIzin,
                    'total_tanpa_keterangan' => $totalTanpaKeterangan
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}