<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (Gate::allows('read-users')) {
            return view('user.index');
        } else {
            return view('error.unauthorize');
        }
    }

    public function data()
    {
        if (!Gate::allows('read-users')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to read users'
            ]);
        }
        $users = User::with('roles')->get();

        return DataTables::of($users)->addIndexColumn()->toJson();
    }

    public function store(Request $request)
    {
        if (!Gate::allows('create-users')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to create users'
            ]);
        }
        try {
            $validate = Validator::make($request->all(), [
                'role' => 'required',
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8'
            ], [
                'role.required' => 'The role field is required',
                'name.required' => 'The name field is required',
                'email.required' => 'The email field is required',
                'email.email' => 'The email must be a valid email address',
                'email.unique' => 'The email has already been taken',
                'password.required' => 'The password field is required',
                'password.min' => 'The password must be at least 8 characters'
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validate->errors()->first()
                ]);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $role = Role::findById($request->role);
            $user->assignRole($role);

            return response()->json([
                'success' => true,
                'message' => 'User ' . $user->name . ' has been created',
                'data' => $user
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
        if (!Gate::allows('update-users')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update users'
            ]);
        }
        try {
            $validate = Validator::make($request->all(), [
                'role' => 'required',
                'name' => 'required',
                'email' => 'required|email|unique:users,email,' . $request->id,
            ], [
                'role.required' => 'The role field is required',
                'name.required' => 'The name field is required',
                'email.required' => 'The email field is required',
                'email.email' => 'The email must be a valid email address',
                'email.unique' => 'The email has already been taken',
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validate->errors()->first()
                ]);
            }

            $user = User::find($request->id);
            $user->name = $request->name;
            $user->email = $request->email;

            $user->save();

            if ($request->role) {
                $role = Role::findById($request->role);
                $user->syncRoles($role);
            }

            return response()->json([
                'success' => true,
                'message' => 'User ' . $user->name . ' has been updated',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
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
        if (!Gate::allows('delete-users')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete users'
            ]);
        }
        try {
            $user = User::find($request->id);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User ' . $user->name . ' has been deleted'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
