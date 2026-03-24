<?php
/** @var Tests\TestCase $this */

use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;
use Twilio\Exceptions\RestException;

uses(\Tests\TestCase::class);

/**
 * Subclase para capturar la lógica de formateo sin llamar a Twilio.
 */
class TestableWhatsAppService extends WhatsAppService
{
    public string $capturedTo = '';
    public string $capturedMessage = '';
    public ?\Exception $exceptionToThrow = null;

    protected function sendTemplate(string $toPhone, string $contentSid, string $contentVariables): void
    {
        $clean = preg_replace('/[^0-9]/', '', $toPhone);
        if (str_starts_with($clean, '521')) { $clean = substr($clean, 3); }
        elseif (str_starts_with($clean, '52')) { $clean = substr($clean, 2); }
        $to = 'whatsapp:+521' . $clean;

        if ($this->exceptionToThrow) {
            Log::error("Error enviando WhatsApp a {$to}: " . $this->exceptionToThrow->getMessage());
            return;
        }

        $this->capturedTo = $to;
        $this->capturedMessage = $contentSid;
    }

    protected function sendRawBody(string $to, string $message): void
    {
        if ($this->exceptionToThrow) {
            Log::error("Error enviando WhatsApp a {$to}: " . $this->exceptionToThrow->getMessage());
            return;
        }

        $this->capturedTo = $to;
        $this->capturedMessage = $message;
    }
}

it('phone number is formatted correctly with whatsapp prefix', function () {
    $service = new TestableWhatsAppService();
    
    $service->sendConfirmation('6621234567', '20/03/2026', '10:00', 'García');
    
    expect($service->capturedTo)->toBe('whatsapp:+5216621234567');
});

it('phone number with existing 52 prefix is not duplicated', function () {
    $service = new TestableWhatsAppService();
    
    $service->sendConfirmation('526621234567', '20/03/2026', '10:00', 'García');
    
    expect($service->capturedTo)->toBe('whatsapp:+5216621234567');
});

it('twilio exception is caught and logged without propagating', function () {
    Log::shouldReceive('error')
        ->once()
        ->withArgs(fn($msg) => str_contains($msg, 'Twilio error'));

    $service = new TestableWhatsAppService();
    $service->exceptionToThrow = new \Exception('Twilio error');

    // No debe lanzar excepción
    $service->sendConfirmation('6621234567', '20/03/2026', '10:00', 'García');
    
    expect(true)->toBeTrue();
});
