<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tus Citas de Hoy</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6; padding: 20px;">
    
    <div style="max-width: 600px; margin: 0 auto; background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h2 style="color: #2563eb; text-align: center;">Tus Citas Médicas para Hoy ({{ $date }})</h2>
        
        <p>Hola <strong>{{ $patient->user->name }}</strong>,</p>
        <p>Te recordamos que tienes las siguientes citas programadas para el día de hoy:</p>

        @if($appointments->isEmpty())
            <div style="background-color: #fef3c7; color: #92400e; padding: 15px; border-radius: 5px; text-align: center;">
                No tienes citas programadas para hoy.
            </div>
        @else
            <ul style="list-style-type: none; padding: 0;">
                @foreach($appointments as $appointment)
                <li style="margin-bottom: 20px; padding: 15px; border: 1px solid #e5e7eb; border-radius: 5px; background-color: #f9fafb;">
                    <strong style="font-size: 16px; color: #1f2937;">Cita con el Dr(a). {{ $appointment->doctor->user->name }}</strong><br>
                    <span style="color: #4b5563;">Hora: {{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }}</span><br>
                    @if($appointment->notes)
                        <span style="color: #6b7280; font-size: 13px;">Notas: {{ $appointment->notes }}</span>
                    @endif
                </li>
                @endforeach
            </ul>
        @endif

        <p>Por favor, llega con 10 minutos de anticipación.</p>

        <p style="margin-top: 30px; font-size: 14px; text-align: center; color: #6b7280;">
            Gracias por su preferencia.
        </p>
    </div>

</body>
</html>
