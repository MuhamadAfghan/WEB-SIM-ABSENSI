<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\KaryawanImport;
use Illuminate\Support\Facades\Storage;

class KaryawanImportController extends Controller
{
    //
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
