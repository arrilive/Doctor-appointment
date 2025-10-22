<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin/dashboard');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Si quieres conservar también /dashboard (no-admin)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Cargar el archivo routes/admin.php con prefijo y nombre
    Route::prefix('admin')->name('admin.')->group(function () {
        require base_path('routes/admin.php');
    });
});
