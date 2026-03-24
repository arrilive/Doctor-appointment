<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Resumen Diario Admin</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6; padding: 20px;">
    
    <div style="max-width: 700px; margin: 0 auto; background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h2 style="color: #2563eb; text-align: center;">Resumen General de Citas ({{ $date }})</h2>
        
        <p>Hola,</p>
        <p>A continuación se presenta el resumen de todas las citas programadas para el día de hoy agrupadas por doctor:</p>

        @if($reportData->isEmpty())
            <div style="background-color: #fef3c7; color: #92400e; padding: 15px; border-radius: 5px; text-align: center;">
                No hay citas programadas para hoy en el sistema.
            </div>
        @else
            @foreach($reportData as $data)
                <h3 style="background-color: #f3f4f6; padding: 10px; border-radius: 5px; margin-top: 30px; font-size: 16px;">
                    Dr(a). {{ $data['doctor_name'] }}
                    <span style="font-size: 14px; color: #6b7280; font-weight: normal;">
                        ({{ $data['appointments']->count() }} citas)
                    </span>
                </h3>

                @if($data['has_appointments'])
                    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                        <thead>
                            <tr style="border-bottom: 2px solid #ddd;">
                                <th style="padding: 8px; text-align: left; font-size: 14px;">Horario</th>
                                <th style="padding: 8px; text-align: left; font-size: 14px;">Paciente</th>
                                <th style="padding: 8px; text-align: left; font-size: 14px;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['appointments'] as $appointment)
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 8px; font-size: 14px;">
                                    {{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }}
                                </td>
                                <td style="padding: 8px; font-size: 14px;">{{ $appointment->patient->user->name ?? 'Desconocido' }}</td>
                                <td style="padding: 8px; font-size: 14px;">{{ ucfirst($appointment->status) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div style="background-color: #fef3c7; color: #92400e; padding: 10px; border-radius: 5px; margin-top: 10px; font-size: 14px;">
                        No appointments scheduled
                    </div>
                @endif
            @endforeach
        @endif

        <p style="margin-top: 40px; font-size: 12px; text-align: center; color: #9ca3af;">
            Este es un reporte automático generado por el Sistema de Citas Médicas.
        </p>
    </div>

</body>
</html>
