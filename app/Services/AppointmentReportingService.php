<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Mail\AppointmentCreatedMail;
use App\Mail\DailyDoctorReportMail;
use App\Mail\DailyPatientReportMail;
use App\Mail\DailyAdminReportMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;

class AppointmentReportingService
{
    /**
     * Generate PDF and send email to doctor and patient when an appointment is created.
     */
    public function sendAppointmentCreatedNotification(Appointment $appointment)
    {
        // Ensure relationships are loaded
        $appointment->load(['patient.user', 'doctor.user', 'doctor.speciality']);
        
        // Generate PDF
        $pdf = Pdf::loadView('pdf.appointment', compact('appointment'));
        
        // Notification to Patient
        if ($appointment->patient && $appointment->patient->user) {
            $testEmail = config('mail.from.address'); // lv20051309@gmail.com
            Mail::to($testEmail)
                ->send(new AppointmentCreatedMail($appointment, $pdf->output(), 'patient'));
            \Illuminate\Support\Facades\Log::info("Email sent to patient {$testEmail} (original: {$appointment->patient->user->email}) for appointment {$appointment->id}");
        }

        // Notification to Doctor
        if ($appointment->doctor && $appointment->doctor->user) {
            $testEmail = config('mail.from.address');
            Mail::to($testEmail)
                ->send(new AppointmentCreatedMail($appointment, $pdf->output(), 'doctor'));
            \Illuminate\Support\Facades\Log::info("Email sent to doctor {$testEmail} (original: {$appointment->doctor->user->email}) for appointment {$appointment->id}");
        }
    }

    /**
     * Send daily appointment report to each doctor for today's appointments.
     */
    public function sendDailyDoctorReports($date)
    {
        $doctors = Doctor::with(['user', 'appointments' => function ($query) use ($date) {
            $query->where('appointment_date', $date)->where('status', '!=', 'cancelado')->orderBy('start_time');
        }, 'appointments.patient.user'])->whereHas('appointments', function ($query) use ($date) {
            $query->where('appointment_date', $date)->where('status', '!=', 'cancelado');
        })->get();

        foreach ($doctors as $doctor) {
            if ($doctor->user) {
                $testEmail = config('mail.from.address');
                Mail::to($testEmail)
                    ->send(new DailyDoctorReportMail($doctor, $doctor->appointments, $date));
                \Illuminate\Support\Facades\Log::info("Daily report sent to doctor {$testEmail} (original: {$doctor->user->email})");
            }
        }
    }

    /**
     * Send daily appointment report to each patient for today's appointments.
     */
    public function sendDailyPatientReports($date)
    {
        $patients = Patient::with(['user', 'appointments' => function ($query) use ($date) {
            $query->where('appointment_date', $date)->where('status', '!=', 'cancelado')->orderBy('start_time');
        }, 'appointments.doctor.user'])->whereHas('appointments', function ($query) use ($date) {
            $query->where('appointment_date', $date)->where('status', '!=', 'cancelado');
        })->get();

        foreach ($patients as $patient) {
            if ($patient->user) {
                $testEmail = config('mail.from.address');
                Mail::to($testEmail)
                    ->send(new DailyPatientReportMail($patient, $patient->appointments, $date));
                \Illuminate\Support\Facades\Log::info("Daily report sent to patient {$testEmail} (original: {$patient->user->email})");
            }
        }
    }

    /**
     * Send daily summary of all appointments to the admin.
     */
    public function sendDailyAdminReport($date, $adminEmail = 'admin@admin.com')
    {
        $allDoctors = Doctor::with('user')->get();
        $appointments = Appointment::with(['patient.user'])
            ->where('appointment_date', $date)
            ->where('status', '!=', 'cancelado')
            ->orderBy('start_time')
            ->get()
            ->groupBy('doctor_id');

        $reportData = collect();

        foreach ($allDoctors as $doctor) {
            $hasAppointments = $appointments->has($doctor->id);
            $reportData->push([
                'doctor_name' => $doctor->user->name ?? 'Doctor sin usuario',
                'has_appointments' => $hasAppointments,
                'appointments' => $hasAppointments ? $appointments->get($doctor->id) : collect()
            ]);
        }

        // Sort: doctors with appointments first, then doctors without
        $reportData = $reportData->sortByDesc('has_appointments')->values();

        $adminEmail = config('mail.from.address'); // Force Admin to .env mail
        Mail::to($adminEmail)->send(new DailyAdminReportMail($reportData, $date));
        \Illuminate\Support\Facades\Log::info("Daily admin report sent to {$adminEmail}");
    }
}
