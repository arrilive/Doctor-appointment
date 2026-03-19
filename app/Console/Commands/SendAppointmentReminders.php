<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:send-reminders';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Send WhatsApp reminders for appointments scheduled for tomorrow';

    /**
     * Execute the console command.
     */
   public function handle(WhatsAppService $whatsAppService): void
    {
        $tomorrow = Carbon::tomorrow()->toDateString();

        $appointments = Appointment::with(['patient.user', 'doctor.user'])
            ->whereDate('appointment_date', $tomorrow)
            ->where('status', 'programado')
            ->orderBy('start_time')
            ->get();

        if ($appointments->isEmpty()) {
            $this->info('Reminders sent: 0');
            return;
        }

        // Armar array para el resumen
        $summary = $appointments->map(fn($appt) => [
            'hora'     => substr($appt->start_time, 0, 5),
            'paciente' => $appt->patient->user->name,
            'doctor'   => $appt->doctor->user->name,
        ])->toArray();

        // Un solo mensaje resumen a tu WhatsApp
        $whatsAppService->sendDailySummary($summary);

        $count = $appointments->count();
        $this->info("Reminders sent: {$count}");
    }
}
