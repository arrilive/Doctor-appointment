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
        // El Sandbox exige plantillas preaprobadas para iniciar la conversación
        // Usaremos la plantilla de Appointment Reminders
        $variables = json_encode(["1" => $fecha, "2" => $hora]);
        $this->sendTemplate($toPhone, 'HXb5b62575e6e4ff6129ad7c8efe1f983e', $variables);
    }

    public function sendReminder(string $toPhone, string $fecha, string $hora, string $doctorName): void
    {
        // Usamos la misma plantilla para el recordatorio
        $variables = json_encode(["1" => $fecha, "2" => $hora]);
        $this->sendTemplate($toPhone, 'HXb5b62575e6e4ff6129ad7c8efe1f983e', $variables);
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

        // Aquí sí enviamos texto libre (body) suponiendo que el admin interactuó hace menos de 24h
        $clean = preg_replace('/[^0-9]/', '', $this->adminTo);
        if (str_starts_with($clean, '521')) { $clean = substr($clean, 3); }
        elseif (str_starts_with($clean, '52')) { $clean = substr($clean, 2); }
        $to = 'whatsapp:+521' . $clean;
        
        $this->sendRawBody($to, implode("\n", $lines));
    }

    // ─── Privados ────────────────────────────────────────────────

    protected function sendTemplate(string $toPhone, string $contentSid, string $contentVariables): void
    {
        $clean = preg_replace('/[^0-9]/', '', $toPhone);
        if (str_starts_with($clean, '521')) {
            $clean = substr($clean, 3);
        } elseif (str_starts_with($clean, '52')) {
            $clean = substr($clean, 2);
        }
        $to = 'whatsapp:+521' . $clean;

        try {
            $client = new Client($this->sid, $this->token);
            $client->messages->create($to, [
                'from' => $this->from,
                'contentSid' => $contentSid,
                'contentVariables' => $contentVariables
            ]);
        } catch (\Exception $e) {
            Log::error("Error enviando WhatsApp Template a {$to}: " . $e->getMessage());
        }
    }

    protected function sendRawBody(string $to, string $message): void
    {
        try {
            $client = new Client($this->sid, $this->token);
            $client->messages->create($to, [
                'from' => $this->from,
                'body' => $message,
            ]);
        } catch (\Exception $e) {
            Log::error("Error enviando WhatsApp (Body) a {$to}: " . $e->getMessage());
        }
    }
}
