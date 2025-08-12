<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class AbsenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function absence(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'date-start' => 'required|date',
            'date-end' => 'required|date|after_or_equal:date-start',
            'type' => 'required|string|max:255',
            'is_approved' => 'boolean',
            'description' => 'nullable|string|max:1000',
            'upload_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        try {
            $absence = Absence::create([
                // 'user_id' => Auth()->id(),
                'user_id' => $request->user_id,
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
}
