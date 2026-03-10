<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController; 
use App\Http\Controllers\Admin\UserController; 
use App\Http\Controllers\Admin\PatientController;

use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\SupportTicketController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\DoctorScheduleController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\ConsultationController;

//Public routes

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

// /admin/support-tickets -> admin.support-tickets.*
Route::resource('support-tickets', SupportTicketController::class)
    ->only(['index', 'create', 'store', 'destroy'])
    ->names('support-tickets');

// /admin/appointments -> admin.appointments.*
Route::resource('appointments', AppointmentController::class)->names('appointments');

// /admin/doctors/{doctor}/schedule -> admin.doctors.schedule.*
Route::get('doctors/{doctor}/schedule', [DoctorScheduleController::class, 'edit'])->name('doctors.schedule.edit');
Route::put('doctors/{doctor}/schedule', [DoctorScheduleController::class, 'update'])->name('doctors.schedule.update');

// /admin/calendar -> admin.calendar.*
Route::get('calendar', [CalendarController::class, 'index'])->name('calendar.index');
Route::get('calendar/events', [CalendarController::class, 'events'])->name('calendar.events');

// /admin/appointments/{appointment}/consultation -> admin.consultations.*
Route::get('appointments/{appointment}/consultation', [ConsultationController::class, 'show'])->name('consultations.show');
Route::post('appointments/{appointment}/consultation', [ConsultationController::class, 'store'])->name('consultations.store');
