<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Cita Médica</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.6; }
        .header { text-align: center; border-bottom: 2px solid #2563eb; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #1e3a8a; }
        .details { margin: 0 auto; width: 80%; border-collapse: collapse; }
        .details th, .details td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        .details th { background-color: #f3f4f6; width: 40%; }
        .footer { margin-top: 40px; text-align: center; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Detalles de tu Cita Médica</h1>
        <p>Confirmación de Reserva</p>
    </div>

    <table class="details">
        <tr>
            <th>Paciente:</th>
            <td>{{ $appointment->patient->user->name }}</td>
        </tr>
        <tr>
            <th>Doctor:</th>
            <td>{{ $appointment->doctor->user->name }} ({{ $appointment->doctor->speciality->name ?? 'General' }})</td>
        </tr>
        <tr>
            <th>Fecha de la cita:</th>
            <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <th>Hora de inicio:</th>
            <td>{{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }}</td>
        </tr>
        <tr>
            <th>Hora de fin:</th>
            <td>{{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }}</td>
        </tr>
        <tr>
            <th>Estado:</th>
            <td>{{ ucfirst($appointment->status) }}</td>
        </tr>
        @if($appointment->notes)
        <tr>
            <th>Notas:</th>
            <td>{{ $appointment->notes }}</td>
        </tr>
        @endif
    </table>

    <div class="footer">
        Este documento fue generado automáticamente por el sistema de Citas Médicas.
    </div>

</body>
</html>
