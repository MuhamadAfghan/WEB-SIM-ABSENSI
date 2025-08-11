<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Attendance;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
}
