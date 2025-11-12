<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController; 
use App\Http\Controllers\Admin\UserController; // 👈 importa UserController también

// /admin/dashboard -> admin.dashboard
Route::get('/dashboard', function () {
    // usa la vista que tengas; si no existe admin/dashboard.blade.php,
    // cambia a view('dashboard')
    return view('dashboard');
})->name('dashboard');

// /admin/roles -> admin.roles.*
Route::resource('roles', RoleController::class)->names('roles');

// /admin/users -> admin.users.*
Route::resource('users', UserController::class)->names('users');
