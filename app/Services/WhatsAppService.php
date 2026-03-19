<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $sid;
    protected string $token;
    protected string $from;
    protected string $adminTo;

    public function __construct()
    {
        $this->sid      = env('TWILIO_SID');
        $this->token    = env('TWILIO_TOKEN');
        $this->from     = 'whatsapp:' . env('TWILIO_WHATSAPP_FROM');
        $this->adminTo  = 'whatsapp:' . env('TWILIO_WHATSAPP_TO');
    }

    /**
     * Confirmación al paciente — usa su teléfono de la DB.
     */
    public function sendConfirmation(string $toPhone, string $fecha, string $hora, string $doctorName): void
    {
        $message = "✅ Cita confirmada para el {$fecha} a las {$hora} con el Dr. {$doctorName}. ¡Te esperamos!";
        $this->sendToPatient($toPhone, $message);
    }

    /**
     * Recordatorio individual por paciente (para uso futuro o pruebas).
     */
    public function sendReminder(string $toPhone, string $fecha, string $hora, string $doctorName): void
    {
        $message = "⏰ Recordatorio: tienes cita mañana {$fecha} a las {$hora} con el Dr. {$doctorName}. ¡No faltes!";
        $this->sendToPatient($toPhone, $message);
    }

    /**
     * Resumen de TODAS las citas del día siguiente — va a tu número (admin).
     */
    public function sendDailySummary(array $appointments): void
    {
        if (empty($appointments)) {
            return;
        }

        $lines = ["📋 *Citas para mañana:*\n"];

        foreach ($appointments as $i => $appt) {
            $n = $i + 1;
            $lines[] = "{$n}. 🕐 {$appt['hora']} — {$appt['paciente']} con el Dr. {$appt['doctor']}";
        }

        $lines[] = "\nTotal: " . count($appointments) . " cita(s).";

        $this->sendRaw($this->adminTo, implode("\n", $lines));
    }

    // ─── Privados ────────────────────────────────────────────────

    protected function sendToPatient(string $toPhone, string $message): void
    {
        $clean = preg_replace('/[^0-9]/', '', $toPhone);
        if (str_starts_with($clean, '52')) {
            $clean = substr($clean, 2);
        }
        $this->sendRaw('whatsapp:+52' . $clean, $message);
    }

    protected function sendRaw(string $to, string $message): void
    {
        try {
            $client = new Client($this->sid, $this->token);
            $client->messages->create($to, [
                'from' => $this->from,
                'body' => $message,
            ]);
        } catch (\Exception $e) {
            Log::error("Error enviando WhatsApp a {$to}: " . $e->getMessage());
        }
    }
}
