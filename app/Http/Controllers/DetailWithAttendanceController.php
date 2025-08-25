<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Absence;

class DetailWithAttendanceController extends Controller
{
    //
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
}
