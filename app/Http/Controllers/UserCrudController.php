<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Exception;

class UserCrudController extends Controller
{
    public function addUser(Request $request)
    {
        try {
            $data = $request->only(['name', 'username', 'password', 'nip', 'email', 'telepon', 'divisi', 'mapel']);
            $data['password'] = Hash::make($data['password']);

            $validator = Validator::make($data, [
                'name' => 'required|string|max:255',
                'username' => ['required', 'string', 'max:255', Rule::unique('users')],
                'password' => 'required|string|min:6',
                'nip' => 'nullable|string|max:20',
                'email' => ['required', 'email', Rule::unique('users')],
                'telepon' => 'nullable|string|max:15',
                'divisi' => 'nullable|string|max:50',
                'mapel' => 'nullable|string|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()], 400);
            }

            $user = User::create($data);
            return response()->json([
                'status' => true,
                'message' => 'User created successfully',
                'data' => $user
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function readAllUser()
    {
        try {
            $data = User::orderBy('id', 'asc')->get()->makeVisible(['password', 'serialNumber']);
            return response()->json([
                'status' => 'success',
                'message' => 'Data found',
                'data' => $data
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
}
