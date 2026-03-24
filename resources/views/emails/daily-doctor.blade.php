<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte Diario Doctor</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6; padding: 20px;">
    
    <div style="max-width: 600px; margin: 0 auto; background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h2 style="color: #2563eb; text-align: center;">Reporte de Citas para Hoy ({{ $date }})</h2>
        
        <p>Hola Dr(a). <strong>{{ $doctor->user->name }}</strong>,</p>
        <p>A continuación se detallan sus citas programadas para el día de hoy:</p>

        @if($appointments->isEmpty())
            <div style="background-color: #fef3c7; color: #92400e; padding: 15px; border-radius: 5px; text-align: center;">
                No appointments scheduled today
            </div>
        @else
            <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <thead>
                    <tr style="background-color: #f3f4f6;">
                        <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Hora</th>
                        <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Paciente</th>
                        <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Notas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appointments as $appointment)
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;">
                            {{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }}
                        </td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $appointment->patient->user->name }}</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $appointment->notes ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <p style="margin-top: 30px; font-size: 14px; text-align: center; color: #6b7280;">
            ¡Que tenga un excelente día de consultas!
        </p>
    </div>

</body>
</html>
