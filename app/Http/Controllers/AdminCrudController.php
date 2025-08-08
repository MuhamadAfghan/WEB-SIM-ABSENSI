<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Exception;

class AdminCrudController extends Controller
{
    public function readAllAdmin()
    {
        try {
            $data = Admin::orderBy('id', 'asc')->get()->makeVisible(['password', 'serialNumber']);
            return response()->json([
                'status' => 'success',
                'message' => 'Data found', 'data' => $data
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server error', 'error' => $e->getMessage()
            ], 500);
        }
    }



    public function showAdminById(?string $id = null)
    {
        try {
            if (is_null($id)) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'ID is required'
                ], 400);
            }

            $data = Admin::find($id);
            if ($data) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data found', 'data' => $data
                ]);
            }
            return response()->json([
                'status' => 'info',
                'message' => 'Data not found'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server error', 'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateAdmin(Request $request, ?string $id = null)
    {
        try {
            if (is_null($id)) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'ID is required'
                ], 400);
            }

            $admindata = Admin::find($id);
            if (!$admindata) return response()->json([
                'status' => false,
                'message' => 'Data not found'
            ], 404);

            $data = array_filter($request->only(['username', 'password',]), fn ($value) => $value !== null);
            if (empty($data)) return response()->json([
                'status' => false,
                'message' => 'No data to update'
            ], 400);

            $validator = Validator::make($data, [
                'username' => ['nullable', 'string', Rule::unique('users', 'username')->ignore($id)],
                'password' => 'nullable|min:6',
            ]);

            if ($validator->fails()) return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);

            $admindata->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Success update data'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server error', 'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteAdmin(?string $id = null)
    {
        try {
            if (is_null($id)) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'ID is required'
                ], 400);
            }

            $admindata = Admin::find($id);
            if (!$admindata) return response()->json(['status' => false, 'message' => 'Data not found'], 404);

            $admindata->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Success delete data'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server error', 'error' => $e->getMessage()
            ], 500);
        }
    }
}
