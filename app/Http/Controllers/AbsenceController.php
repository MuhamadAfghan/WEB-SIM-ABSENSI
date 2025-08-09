<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AbsenceExport;
use Illuminate\Http\Request;

class AbsenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    
    public function filter(Request $request)
    {
        try {
        $query = Absence::query();

        $filters = [ 
            'date_start' => $request->date_start,
            'date_end' => $request->date_end,
            'type' => $request->type ?? 'all'
        ];

        if ($request->has('date_start')) {
            $query->where('date-start', '>=', $request->date_start);
        }

        if ($request->has('date_end')) {
            $query->where('date-end', '<=', $request->date_end);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        //Get results with pagination
        $absences = $query->with('user')->paginate(10);

        return response()->json([
            'filters_applied' => $filters,
            'data' => $absences,
            'total_records' => $absences->total(),
            'current_page' => $absences->currentPage(),
            'per_page' => $absences->perPage(),
        ]);
        } catch (\Exception $e) {
        return response()->json([
            'error' => 'An error occurred while filtering absences: ' . $e->getMessage(),
            'filters_attempted' => [
                'date_start' => $request->date_start,
                'date_end' => $request->date_end,
                'type' => $request->type ?? 'all'
            ]
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
