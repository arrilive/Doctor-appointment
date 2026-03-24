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

    public function sendConfirmation(string $toPhone, string $fecha, string $hora, string $doctorName): void
    {
        $message = "✅ Cita confirmada para el {$fecha} a las {$hora} con el Dr. {$doctorName}. ¡Te esperamos!";
        $this->sendToPatient($toPhone, $message);
    }

    public function sendReminder(string $toPhone, string $fecha, string $hora, string $doctorName): void
    {
        $message = "⏰ Recordatorio: tienes cita mañana {$fecha} a las {$hora} con el Dr. {$doctorName}. ¡No faltes!";
        $this->sendToPatient($toPhone, $message);
    }

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

        $clean = preg_replace('/[^0-9]/', '', $this->adminTo);
        if (str_starts_with($clean, '52')) {
            $clean = substr($clean, 2);
        }
        $to = 'whatsapp:+52' . $clean;
        
        $this->sendRawBody($to, implode("\n", $lines));
    }

    // ─── Privados ────────────────────────────────────────────────

    protected function sendToPatient(string $toPhone, string $message): void
    {
        $clean = preg_replace('/[^0-9]/', '', $toPhone);
        
        // STRICT REQUIREMENT: Output whatsapp:+529993623163 (NO +521)
        if (str_starts_with($clean, '521')) { 
            $clean = substr($clean, 3); 
        } elseif (str_starts_with($clean, '52')) { 
            $clean = substr($clean, 2); 
        }
        
        $to = 'whatsapp:+52' . $clean;
        
        $this->sendRawBody($to, $message);
    }

    protected function sendRawBody(string $to, string $message): void
    {
        try {
            $client = new Client($this->sid, $this->token);
            // STRICT REQUIREMENT: Use ONLY body messages
            $response = $client->messages->create($to, [
                'from' => $this->from,
                'body' => $message,
            ]);
            
            // STRICT REQUIREMENT: Add logging of Twilio message SID for debugging
            Log::info("Twilio Message Delivered Successfully - SID: {$response->sid} - To: {$to}");
        } catch (\Exception $e) {
            Log::error("Error enviando WhatsApp (Body) a {$to}: " . $e->getMessage());
        }
    }
}
