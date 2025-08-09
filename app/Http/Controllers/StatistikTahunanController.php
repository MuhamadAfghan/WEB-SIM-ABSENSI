<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class StatistikTahunanController extends Controller
{
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
}
