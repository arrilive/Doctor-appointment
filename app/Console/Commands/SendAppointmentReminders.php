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

        // Armar array para el resumen y además enviar el recordatorio individual a cada paciente
        $summary = [];
        foreach ($appointments as $appt) {
            $horaStr = substr($appt->start_time, 0, 5);
            $fechaStr = $appt->appointment_date->format('d/m/Y');
            
            // Enviar a cada paciente
            $whatsAppService->sendReminder(
                $appt->patient->user->phone,
                $fechaStr,
                $horaStr,
                $appt->doctor->user->name
            );

            // Guardar para el resumen del admin
            $summary[] = [
                'hora'     => $horaStr,
                'paciente' => $appt->patient->user->name,
                'doctor'   => $appt->doctor->user->name,
            ];
        }

        // Un solo mensaje resumen a tu WhatsApp
        $whatsAppService->sendDailySummary($summary);

        $count = $appointments->count();
        $this->info("Reminders sent: {$count}");
    }
}
