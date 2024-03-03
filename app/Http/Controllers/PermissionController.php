<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('permission.index');
    }

    public function data()
    {
        $permissions = Permission::all();
        return DataTables::of($permissions)->addIndexColumn()->toJson();
    }

    public function store(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'permission' => 'required'
            ], [
                'permission.required' => 'The permission field is required'
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validate->errors()->first()
                ]);
            }

            $checkPermission = Permission::where('name', $request->permission)->first();
            if ($checkPermission) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permssion ' . $request->permission . ' already exists'
                ]);
            }

            $permission = Permission::create([
                'name' => $request->permission,
                'guard_name' => 'web'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permission ' . $permission->name . ' has been created',
                'data' => $permission
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
                'permission' => 'required'
            ], [
                'permission.required' => 'The permission field is required'
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validate->errors()->first()
                ]);
            }

            $permission = Permission::find($request->id);
            $checkPermission = Permission::where('name', $request->permission)->first();
            if ($checkPermission || $permission->name == $request->permission) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permission ' . $request->permission . ' already exists'
                ]);
            }

            $permission->name = $request->permission;
            $permission->save();

            return response()->json([
                'success' => true,
                'message' => 'Permission ' . $permission->name . ' has been updated',
                'data' => $permission
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
            $permission = Permission::find($request->id);
            $permission->delete();

            return response()->json([
                'success' => true,
                'message' => 'Permission ' . $permission->name . ' has been deleted'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
