<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AbsenceExport;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Exception;

class AbsenceController extends Controller
{
    public function approveAbsence(User $user)
    {
        $absence = Absence::where('user_id', $user->id)->first();

        if (!$absence) {
            return response()->json(['status' => 'error', 'message' => 'Absence not found'], 404);
        }

        $absence->is_approved = true;
        $absence->save();

        return response()->json(['status' => 'success', 'message' => 'Absence approved successfully']);
    }

    /**
     * Display a listing of the resource.
     */

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
    public function show(Absence $absence)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Absence $absence)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Absence $absence)
    {
        //
    }

    protected $rules = [
        'date_start' => 'required|date',
        'date_end' => 'required|date|after_or_equal:date_start',
        'type' => 'nullable|string|in:izin,sakit,alpa',
    ];

    // Add error messages
    protected $messages = [
        'date_start.required' => 'Tanggal mulai wajib diisi',
        'date_end.required' => 'Tanggal selesai wajib diisi',
        'date_end.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai'
    ];

    public function showAbsences(Request $request)
    {
        try {
            // Get Absence and Attendance data, merge before filtering
            $absenceQuery = Absence::query()->with('user');
            $attendanceQuery = Attendance::query()->with('user');

            // Merge collections
            $absences = $absenceQuery->get();
            $attendances = $attendanceQuery->get();

            // Combine and convert to collection
            $merged = $absences->concat($attendances);

            // Apply filters
            if ($request->has('date_start')) {
                $merged = $merged->filter(function ($item) use ($request) {
                    return isset($item->date_start)
                        ? $item->date_start >= $request->date_start
                        : (isset($item->date) ? $item->date >= $request->date_start : true);
                });
            }

            if ($request->has('date_end')) {
                $merged = $merged->filter(function ($item) use ($request) {
                    return isset($item->date_end)
                        ? $item->date_end <= $request->date_end
                        : (isset($item->date) ? $item->date <= $request->date_end : true);
                });
            }

            if ($request->has('type')) {
                $merged = $merged->filter(function ($item) use ($request) {
                    return isset($item->type) ? $item->type == $request->type : true;
                });
            }

            // Paginate manually
            $page = $request->get('page', 1);
            $perPage = 10;
            $items = $merged->slice(($page - 1) * $perPage, $perPage)->values();
            $total = $merged->count();

            return response()->json([
                'status' => 'success',
                'message' => 'Absences and attendances filtered successfully',
                'data' => [
                    'filters_applied' => [
                        'date_start' => $request->date_start,
                        'date_end' => $request->date_end,
                        'type' => $request->type ?? 'all'
                    ],
                    'items' => $items,
                    'total_records' => $total,
                    'current_page' => $page,
                    'per_page' => $perPage,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to filter absences and attendances',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'date_start' => 'required|date',
                'date_end' => 'required|date|after_or_equal:date_start',
            ], $this->messages);

            $fileName = 'absences-report-' . date('Y-m-d') . '.xlsx';

            // Simplified return without headers
            return Excel::download(new AbsenceExport($request->date_start, $request->date_end), $fileName);
        } catch (\Exception $e) {
            \Log::error('Export error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to export data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Absence $absence)
    {
        //
    }
}
