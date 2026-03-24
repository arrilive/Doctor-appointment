<?php

namespace App\Console\Commands;

use App\Services\AppointmentReportingService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendDailyAppointmentReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:daily-appointments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends the daily appointment reports to Doctors, Patients, and Admin.';

    /**
     * Execute the console command.
     */
    public function handle(AppointmentReportingService $reportingService)
    {
        $today = Carbon::today()->format('Y-m-d');
        $this->info("Iniciando el envío de reportes para el día: {$today}");

        // In a real application, consider using DB transactions or try-catch for each block.
        try {
            // Patient Reports
            $this->info("Enviando reportes a pacientes...");
            $reportingService->sendDailyPatientReports($today);
            
            // Doctor Reports
            $this->info("Enviando reportes a doctores...");
            $reportingService->sendDailyDoctorReports($today);
            
            // Admin Report
            $this->info("Enviando reporte resumen al administrador...");
            $reportingService->sendDailyAdminReport($today);
            
            $this->info("Todos los reportes diarios han sido enviados correctamente.");
        } catch (\Exception $e) {
            $this->error("Hubo un error al enviar los reportes: " . $e->getMessage());
            \Illuminate\Support\Facades\Log::error("Error en SendDailyAppointmentReports: " . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
