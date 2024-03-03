<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('role.index');
    }

    public function data()
    {
        $roles = Role::with('permissions')->get();

        return DataTables::of($roles)->addIndexColumn()->toJson();
    }

    public function store(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'role' => 'required'
            ], [
                'role.required' => 'The role field is required'
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validate->errors()->first()
                ]);
            }

            $checkRole = Role::where('name', $request->role)->first();
            if ($checkRole) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role ' . $request->role . ' already exists'
                ]);
            }

            $role = Role::create([
                'name' => $request->role,
                'guard_name' => 'web'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Role ' . $role->name . ' has been created',
                'data' => $role
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'role' => 'required'
            ], [
                'role.required' => 'The role field is required'
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validate->errors()->first()
                ]);
            }

            $role = Role::find($request->id);
            $checkRole = Role::where('name', $request->role)->first();
            if ($checkRole || $role->name == $request->role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role ' . $request->role . ' already exists'
                ]);
            }

            $role->name = $request->role;
            $role->save();

            return response()->json([
                'success' => true,
                'message' => 'Role ' . $role->name . ' has been updated',
                'data' => $role
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $role = Role::find($request->id);

            $role->syncPermissions();

            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Role ' . $role->name . ' has been deleted'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
