<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Fetching latest appointment...\n";
$appointment = App\Models\Appointment::with(['patient.user', 'doctor.user'])->latest()->first();

if (!$appointment) {
    echo "No appointments found.\n";
    exit;
}

echo "Appointment ID: " . $appointment->id . "\n";
echo "Patient Phone: " . $appointment->patient->user->phone . "\n";

$whatsAppService = app(\App\Services\WhatsAppService::class);

echo "Attempting to send confirmation...\n";
try {
    $whatsAppService->sendConfirmation(
        $appointment->patient->user->phone,
        $appointment->appointment_date->format('d/m/Y'),
        substr($appointment->start_time, 0, 5),
        $appointment->doctor->user->name
    );
    echo "DONE! Method executed without exceptions.\n";
} catch (\Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
}

// Check recent logs
echo "\n--- Recent Laravel Logs ---\n";
echo shell_exec('tail -n 15 storage/logs/laravel.log');
