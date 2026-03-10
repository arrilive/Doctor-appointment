<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Vista principal del calendario.
     */
    public function index()
    {
        $doctors = Doctor::with('user')->get();
        return view('admin.calendar.index', compact('doctors'));
    }

    /**
     * Endpoint JSON para FullCalendar.
     * Devuelve:
     *   - Eventos de fondo (background) = disponibilidad del doctor (verde)
     *   - Eventos normales = citas (azul/verde/rojo según estado)
     */
    public function events(Request $request)
    {
        $doctorId = $request->input('doctor_id');
        $start    = $request->input('start');
        $end      = $request->input('end');

        if (!$doctorId) {
            return response()->json([]);
        }

        $doctor = Doctor::with(['user', 'schedules'])->find($doctorId);
        if (!$doctor) {
            return response()->json([]);
        }

        $events = [];

        // 1. Disponibilidad semanal como eventos de fondo (verde)
        foreach ($doctor->schedules as $schedule) {
            // DB: 0=Lunes…6=Domingo → FullCalendar: 0=Dom,1=Lun…6=Sab
            $fcDay = ($schedule->day_of_week + 1) % 7;

            $events[] = [
                'groupId'    => 'available',
                'title'      => '',
                'startTime'  => substr($schedule->start_time, 0, 5),
                'endTime'    => substr($schedule->end_time, 0, 5),
                'daysOfWeek' => [$fcDay],
                'display'    => 'background',
                'color'      => '#bbf7d0',
                'classNames' => ['schedule-bg'],
            ];
        }

        // 2. Citas como eventos normales
        $startDate = \Carbon\Carbon::parse($start)->format('Y-m-d');
        $endDate   = \Carbon\Carbon::parse($end)->format('Y-m-d');

        $appointments = Appointment::where('doctor_id', $doctorId)
            ->with('patient.user')
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->get();

        $colorMap = [
            'programado' => '#3b82f6',  // azul
            'completado' => '#10b981',  // verde
            'cancelado'  => '#ef4444',  // rojo
        ];

        foreach ($appointments as $apt) {
            $color     = $colorMap[$apt->status] ?? '#6b7280';
            $patName   = $apt->patient->user->name ?? 'Paciente';
            $statusMap = ['programado' => 'Programado', 'completado' => 'Completado', 'cancelado' => 'Cancelado'];

            $events[] = [
                'id'              => $apt->id,
                'title'           => $patName,
                'start'           => $apt->appointment_date->format('Y-m-d') . 'T' . substr($apt->start_time, 0, 5),
                'end'             => $apt->appointment_date->format('Y-m-d') . 'T' . substr($apt->end_time, 0, 5),
                'backgroundColor' => $color,
                'borderColor'     => $color,
                'textColor'       => '#ffffff',
                'url'             => route('admin.appointments.edit', $apt->id),
                'extendedProps'   => [
                    'status'     => $apt->status,
                    'statusText' => $statusMap[$apt->status] ?? $apt->status,
                    'notes'      => $apt->notes,
                ],
            ];
        }

        return response()->json($events);
    }
}
