<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;

class DoctorScheduleController extends Controller
{
    /**
     * Muestra el gestor de horarios del doctor.
     */
    public function edit(Doctor $doctor)
    {
        $doctor->load(['user', 'schedules']);

        // Franjas horarias: 08:00 a 20:00 en bloques de 30 min
        $timeSlots = [];
        $start = \Carbon\Carbon::createFromTime(8, 0);
        $end   = \Carbon\Carbon::createFromTime(20, 0);
        while ($start < $end) {
            $slotStart = $start->format('H:i');
            $start->addMinutes(30);
            $slotEnd = $start->format('H:i');
            $timeSlots[] = ['start' => $slotStart, 'end' => $slotEnd];
        }

        // Índice de disponibilidad existente: [dia][start_time HH:MM] => true
        $existing = [];
        foreach ($doctor->schedules as $s) {
            // MySQL time columns return "08:00:00", normalize to "08:00"
            $existing[$s->day_of_week][substr($s->start_time, 0, 5)] = true;
        }

        $days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

        return view('admin.doctors.schedule', compact('doctor', 'timeSlots', 'days', 'existing'));
    }

    /**
     * Guarda (reemplaza) la disponibilidad del doctor.
     */
    public function update(Request $request, Doctor $doctor)
    {
        // slots es un array de "dayOfWeek_startTime_endTime" que llegaron marcados
        $slots = $request->input('slots', []);

        // Borrar toda la disponibilidad anterior
        DoctorSchedule::where('doctor_id', $doctor->id)->delete();

        // Insertar los horarios seleccionados
        foreach ($slots as $slot) {
            [$day, $slotStart, $slotEnd] = explode('_', $slot);
            DoctorSchedule::create([
                'doctor_id'   => $doctor->id,
                'day_of_week' => (int) $day,
                'start_time'  => $slotStart,
                'end_time'    => $slotEnd,
            ]);
        }

        return redirect()->back()->with('swal', [
            'icon'  => 'success',
            'title' => 'Horario guardado',
            'text'  => 'La disponibilidad del doctor ha sido actualizada.',
        ]);
    }
}
