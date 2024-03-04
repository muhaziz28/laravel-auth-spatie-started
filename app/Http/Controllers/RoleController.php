<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
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

    public function assignPermission(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'role' => 'required',
                'permissions' => 'required|array'
            ], [
                'role.required' => 'The role field is required',
                'permissions.required' => 'The permissions field is required'
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validate->errors()->first()
                ]);
            }

            $role = Role::where('name', $request->role)->first();
            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role ' . $request->role . ' not found'
                ]);
            }

            $permissions = Permission::whereIn('id', $request->permissions)->get();
            if ($permissions->count() !== count($request->permissions)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some permissions not found'
                ]);
            }

            $existingPermissions = $role->permissions->pluck('id')->toArray();

            $deletedPermissions = array_diff($existingPermissions, $request->permissions);

            if (!empty($deletedPermissions)) $role->permissions()->detach($deletedPermissions);

            if ($role->permissions->isEmpty()) {
                $role->permissions()->attach($permissions);
            } else {
                $role->permissions()->syncWithoutDetaching($permissions);
            }

            return response()->json([
                'success' => true,
                'message' => 'Permissions have been assigned to role ' . $role->name
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
