<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\KaryawanImport;

class UserCrudController extends Controller
{
    // public function readAllUser()
    // {
    //     try {
    //         $data = User::orderBy('id', 'asc')->get()->makeVisible(['password', 'serialNumber']);
    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Data found', 'data' => $data
    //         ]);
    //     } catch (Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Server error', 'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function addUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'nip' => 'required|string|max:20|unique:users',
                'email' => ['required', 'email', 'max:255', Rule::unique('users')],
                'password' => 'nullable|string|min:6',
                'telepon' => 'nullable|string|max:15',
                'divisi' => 'nullable|string|max:100',
                'mapel' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()], 400);
            }

            $password = $request->filled('password') ? $request->password : $request->nip;

            $user = User::create([
                'name' => $request->name,
                'password' => Hash::make($password),
                'nip' => $request->nip,
                'email' => $request->email,
                'telepon' => $request->telepon,
                'divisi' => $request->divisi,
                'mapel' => $request->mapel,
            ]);

            return response()->json(['status' => true, 'message' => 'User created successfully', 'data' => $user], 201);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }

    public function readAllUser(Request $request)
    {
        try {
            $query = User::query();

            // Fitur pencarian
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%")
                        ->orWhere('nip', 'like', "%$search%")
                        ->orWhere('telepon', 'like', "%$search%")
                        ->orWhere('divisi', 'like', "%$search%")
                        ->orWhere('mapel', 'like', "%$search%");
                });
            }

            // Fitur sorting
            $sortField = $request->input('sort_field', 'id');
            $sortOrder = $request->input('sort_order', 'asc');

            // Validasi field sorting untuk mencegah SQL injection
            $validSortFields = ['id', 'name', 'username', 'email', 'nip', 'telepon', 'divisi', 'mapel', 'created_at', 'updated_at'];
            if (!in_array($sortField, $validSortFields)) {
                $sortField = 'id';
            }

            $sortOrder = strtolower($sortOrder) === 'desc' ? 'desc' : 'asc';

            $query->orderBy($sortField, $sortOrder);

            // Fitur pagination
            $perPage = $request->input('per_page', 10);
            $users = $query->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'Data found',
                'data' => $users->items(),
                'meta' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'sort_field' => $sortField,
                    'sort_order' => $sortOrder,
                    'search_query' => $request->search ?? null,
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function showUserById(?string $id = null)
    {
        try {
            if (is_null($id)) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'ID is required'
                ], 400);
            }

            $data = User::find($id);
            if ($data) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data found',
                    'data' => $data
                ]);
            }
            return response()->json([
                'status' => 'info',
                'message' => 'Data not found'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateUser(Request $request, ?string $id = null)
    {
        try {
            if (is_null($id)) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'ID is required'
                ], 400);
            }

            $userdata = User::find($id);
            if (!$userdata) return response()->json([
                'status' => false,
                'message' => 'Data not found'
            ], 404);

            $data = array_filter($request->only(['name', 'username', 'password', 'nip', 'email', 'telepon', 'divisi', 'mapel']), fn($value) => $value !== null);
            if (empty($data)) return response()->json([
                'status' => false,
                'message' => 'No data to update'
            ], 400);

            $validator = Validator::make($data, [
                'name' => 'nullable',
                'username' => ['nullable', 'string', Rule::unique('users', 'username')->ignore($id)],
                'password' => 'nullable|min:6',
                'nip' => 'nullable',
                'email' => ['nullable', 'email', Rule::unique('users', 'email')->ignore($id)],
                'telepon' => 'nullable',
                'divisi' => 'nullable',
                'mapel' => 'nullable',
            ]);

            if ($validator->fails()) return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);

            $userdata->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Success update data'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteUser(?string $id = null)
    {
        try {
            if (is_null($id)) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'ID is required'
                ], 400);
            }

            $userdata = User::find($id);
            if (!$userdata) return response()->json(['status' => false, 'message' => 'Data not found'], 404);

            $userdata->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Success delete data'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

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
