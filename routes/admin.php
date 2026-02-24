<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController; 
use App\Http\Controllers\Admin\UserController; 
use App\Http\Controllers\Admin\PatientController;

use App\Http\Controllers\Admin\DoctorController;

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

// /admin/patients -> admin.patients.*
Route::resource('patients', PatientController::class)->except(['create', 'store'])->names('patients');

// /admin/doctors -> admin.doctors.*
Route::resource('doctors', DoctorController::class)->except(['create', 'store'])->names('doctors');