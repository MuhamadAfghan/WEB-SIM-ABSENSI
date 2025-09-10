<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AbsenceExport;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Setting;
use Carbon\Carbon;
use Exception;

class AbsenceController extends Controller
{
    // public function approveAbsence(User $user)
    // {
    //     $absence = Absence::where('user_id', $user->id)->first();

    //     if (!$absence) {
    //         return response()->json(['status' => 'error', 'message' => 'Absence not found'], 404);
    //     }

    //     $absence->is_approved = true;
    //     $absence->save();

    //     return response()->json(['status' => 'success', 'message' => 'Absence approved successfully']);
    // }

    /**
     * Display a listing of the resource.
     */
    public function absence(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date-start' => 'required|date',
            'date-end' => 'required|date|after_or_equal:date-start',
            'type' => 'required|string|max:255|in:izin,sakit,tanpa_keterangan',
            'is_approved' => 'boolean',
            'description' => 'required|string|max:1000',
            'upload_attachment' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $userId = $request->user()->id;
        $today = Carbon::today()->format('Y-m-d');
        $currentTime = Carbon::now();
        $dayName = strtolower($currentTime->format('l')); // monday, tuesday, etc.

        // Cek apakah sudah absen masuk hari ini
        $existingAttendance = Attendance::where('user_id', $userId)
            ->where('date', $today)
            ->whereNotNull('check_in_time')
            ->exists();

        // cek from absence
        $existingAbsence = Absence::where('user_id', $userId)
            ->where('created_at', '>=', $today . ' 00:00:00')
            ->where('created_at', '<=', $today . ' 23:59:59')
            ->exists();

        if ($existingAttendance || $existingAbsence) {
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

        try {
            $absence = Absence::create([
                'user_id' => $request->user()->id,
                'date-start' => $request->input('date-start'),
                'date-end' => $request->input('date-end'),
                'type' => $request->type,
                'is_approved' => $request->is_approved ?? false,
                'description' => $request->description,
                'upload_attachment' => $request->file('upload_attachment') ? $request->file('upload_attachment')->store('attachments') : null
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $absence
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create absence record: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approve(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'is_approved' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        try {
            $absence = Absence::find($id);

            if (!$absence) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Absence record not found'
                ], 404);
            }

            $absence->update([
                'is_approved' => $request->is_approved
            ]);

            $status = $request->is_approved ? 'approved' : 'rejected';

            return response()->json([
                'status' => 'success',
                'message' => "Absence has been {$status}",
                'data' => $absence
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update absence: ' . $e->getMessage()
            ], 500);
        }
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

    public function showAbsenceById($id)
    {
        try {
            $absence = Absence::with('user')->find($id);

            if (!$absence) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Absence record not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $absence
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve absence record: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showAbsences(Request $request)
    {
        try {
            // Get Absence and Attendance data, merge before filtering
            $absenceQuery = Absence::query()->with('user');
            $attendanceQuery = Attendance::query()->with('user');

            // Merge collections
            $absences = $absenceQuery->get()->map(function ($absence) {
                $absence->absence_status = 'tidak hadir';
                return $absence;
            });
            $attendances = $attendanceQuery->get()->map(function ($attendance) {
                $attendance->absence_status = 'hadir';
                return $attendance;
            });

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
            // sort by created_at
            $items = $items->sortByDesc('created_at');
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
