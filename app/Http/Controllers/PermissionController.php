<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
        if (Gate::allows('read-permissions')) return view('permission.index');
        return view('error.unauthorize');
    }

    public function data()
    {
        if (Gate::allows('read-permissions')) {
            $permissions = Permission::all();
            return DataTables::of($permissions)->addIndexColumn()->toJson();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to read permissions'
            ]);
        }
    }

    public function store(Request $request)
    {
        if (Gate::allows('create-permissions')) {
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
        } else {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to create permissions'
            ]);
        }
    }

    public function update(Request $request)
    {
        if (Gate::allows('update-permissions')) {
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
        } else {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update permissions'
            ]);
        }
    }

    public function destroy(Request $request)
    {
        if (Gate::allows('delete-permissions')) {
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
        } else {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete permissions'
            ]);
        }
    }
}
