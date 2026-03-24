<?php
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use Twilio\Rest\Client;

$sid    = $_ENV['TWILIO_SID'];
$token  = $_ENV['TWILIO_TOKEN'];
$from   = 'whatsapp:' . $_ENV['TWILIO_WHATSAPP_FROM'];
$to     = 'whatsapp:' . $_ENV['TWILIO_WHATSAPP_TO'];

echo "Sending from: $from\n";
echo "Sending to: $to\n";

try {
    $client = new Client($sid, $token);
    // Let's use the template EXACTLY as the sandbox expects just in case freeform is blocked!
    $message = $client->messages->create($to, [
        'from' => $from,
        'contentSid' => 'HXb5b62575e6e4ff6129ad7c8efe1f983e',
        'contentVariables' => json_encode(["1" => "12/1", "2" => "3pm"])
    ]);
    echo "Exito! Message SID: " . $message->sid . "\n";
} catch (\Exception $e) {
    echo "Excepcion de Twilio:\n" . $e->getMessage() . "\n";
}
