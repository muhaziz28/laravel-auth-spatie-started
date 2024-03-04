<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // redirect ke halaman login jika belum login
    if (!Auth::check()) {
        return redirect('/login');
    }
    return view('/home');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// middleware('auth') digunakan untuk membatasi akses ke halaman ini hanya untuk user yang sudah login
Route::middleware('auth')->group(function () {
    Route::controller(RoleController::class)->prefix('role')->group(function () {
        Route::get('', 'index')->name('role.index');
        Route::get('data', 'data')->name('role.data');
        Route::post('store', 'store')->name('role.store');
        Route::put('update', 'update')->name('role.update');
        Route::delete('destroy', 'destroy')->name('role.destroy');
        Route::post('assign-permission', 'assignPermission')->name('role.assignPermission');
    });
    Route::controller(PermissionController::class)->prefix('permission')->group(function () {
        Route::get('', 'index')->name('permission.index');
        Route::get('data', 'data')->name('permission.data');
        Route::post('store', 'store')->name('permission.store');
        Route::put('update', 'update')->name('permission.update');
        Route::delete('destroy', 'destroy')->name('permission.destroy');
    });
});
