<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cita Agendada</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6; padding: 20px;">
    
    <div style="max-width: 600px; margin: 0 auto; background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h2 style="color: #2563eb; text-align: center;">Confirmación de Cita Médica</h2>
        
        @if($recipientType === 'doctor')
            <p>Hola Dr(a). <strong>{{ $appointment->doctor->user->name }}</strong>,</p>
            <p>Se ha agendado una nueva cita en su calendario:</p>
        @else
            <p>Hola <strong>{{ $appointment->patient->user->name }}</strong>,</p>
            <p>Su cita médica ha sido confirmada con éxito:</p>
        @endif

        <ul style="list-style-type: none; padding: 0;">
            <li style="margin-bottom: 10px;"><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}</li>
            <li style="margin-bottom: 10px;"><strong>Hora:</strong> {{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }}</li>
            @if($recipientType === 'doctor')
                <li style="margin-bottom: 10px;"><strong>Paciente:</strong> {{ $appointment->patient->user->name }}</li>
            @else
                <li style="margin-bottom: 10px;"><strong>Doctor:</strong> {{ $appointment->doctor->user->name }}</li>
            @endif
        </ul>

        <p>Adjunto a este correo encontrará el comprobante en PDF con los detalles de la cita.</p>

        <p style="margin-top: 30px; font-size: 14px; text-align: center; color: #6b7280;">
            Gracias por confiar en nuestros servicios.
        </p>
    </div>

</body>
</html>
