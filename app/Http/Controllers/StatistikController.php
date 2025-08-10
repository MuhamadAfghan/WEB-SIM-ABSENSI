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
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $tahun = (int) $request->input('year');
            $data = [];

            // Siapkan struktur data 12 bulan
            for ($i = 1; $i <= 12; $i++) {
                $namaBulan = Carbon::createFromDate($tahun, $i, 1)->locale('id')->translatedFormat('F');
                $data[$i] = [
                    'bulan' => $namaBulan,
                    'hadir' => 0,
                    'sakit' => 0,
                    'izin' => 0,
                    'tanpa_keterangan' => 0,
                ];
            }

            // Hitung user yang hadir per bulan (user unik)
            $hadir = DB::table('attendances')
                ->selectRaw('MONTH(date) as bulan, COUNT(DISTINCT user_id) as total')
                ->whereYear('date', $tahun)
                ->groupBy(DB::raw('MONTH(date)'))
                ->get();

            foreach ($hadir as $h) {
                $data[$h->bulan]['hadir'] = $h->total;
            }

            // Ambil semua absensi selain hadir (izin, sakit, tanpa_keterangan)
            $absences = DB::table('absences')
                ->select('user_id', 'type', 'date-start', 'date-end')
                ->where(function ($q) use ($tahun) {
                    $q->whereYear('date-start', $tahun)
                        ->orWhereYear('date-end', $tahun);
                })
                ->get();

            $userBulanTipe = []; // Cegah duplikat hitungan user di bulan yang sama

            foreach ($absences as $row) {
                $start = Carbon::parse($row->{'date-start'});
                $end = Carbon::parse($row->{'date-end'});

                while ($start <= $end) {
                    if ($start->year == $tahun) {
                        $bulan = $start->month;
                        $key = $row->user_id . '-' . $bulan . '-' . $row->type;

                        if (!isset($userBulanTipe[$key])) {
                            if (isset($data[$bulan][$row->type])) {
                                $data[$bulan][$row->type]++;
                                $userBulanTipe[$key] = true;
                            }
                        }
                    }
                    $start->addDay();
                }
            }

            // Total tahunan semua jenis
            $rekapTahunan = [
                'hadir' => 0,
                'sakit' => 0,
                'izin' => 0,
                'tanpa_keterangan' => 0,
            ];

            foreach ($data as $bulan) {
                foreach ($rekapTahunan as $key => $_) {
                    $rekapTahunan[$key] += $bulan[$key];
                }
            }

            return response()->json([
                'success' => true,
                "message" => "Data Statistik Tahunan",
                "data" => [
                    'tahun' => $tahun,
                    'total_statistik_tahunan' => $rekapTahunan,
                    'data_bulanan' => array_values($data)
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
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
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Definisikan array months di dalam fungsi statistikBulanan
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

            // Ambil bulan dari nama
            $monthName = strtolower($request->input('month'));
            $month = $months[$monthName] ?? null;

            if (!$month) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nama bulan tidak valid',
                ], 422);
            }

            $year = (int) ($request->input('year') ?? date('Y'));

            // Rekap data absensi
            $data = DB::table('attendances')
                ->selectRaw("
                SUM(CASE WHEN keterangan = 'hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN keterangan = 'sakit' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN keterangan = 'izin' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN keterangan IS NULL OR keterangan = '' THEN 1 ELSE 0 END) as tanpa_keterangan
            ")
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->first();

            // Nama bulan Indonesia
            $indonesianMonth = Carbon::createFromDate($year, $month, 1)
                ->locale('id')
                ->translatedFormat('F');

            return response()->json([
                'success' => true,
                'message' => 'Data Statistik Bulanan',
                'data' => [
                    'bulan' => strtolower($indonesianMonth),
                    'hadir' => (int) $data->hadir,
                    'sakit' => (int) $data->sakit,
                    'izin' => (int) $data->izin,
                    'tanpa_keterangan' => (int) $data->tanpa_keterangan,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
