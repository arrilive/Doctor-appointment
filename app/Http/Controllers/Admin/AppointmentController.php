<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Speciality;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /** Available appointment durations (minutes). */
    public const DURATIONS = [
        60  => '1 hora',
        90  => '1 hora 30 min',
        120 => '2 horas',
    ];

    public function index()
    {
        return view('admin.appointments.index');
    }

    public function create(Request $request)
    {
        $specialities = Speciality::orderBy('name')->get();
        $patients     = Patient::with('user')->get();
        $durations    = self::DURATIONS;
        $availableDoctors = collect();

        if ($request->filled(['date', 'start_time', 'duration'])) {
            $date      = $request->date;
            $startTime = $request->start_time;
            $duration  = (int) $request->duration; // minutes
            $endTime   = Carbon::createFromFormat('H:i', $startTime)->addMinutes($duration)->format('H:i');

            // Day of week: 0=Lunes … 6=Domingo (Carbon isoWeekday: Mon=1 … Sun=7, we store 0-6)
            $dayOfWeek  = (int) Carbon::parse($date)->format('N') - 1;
            $slotsNeeded = $duration / 30; // each block is 30 min

            $availableDoctors = Doctor::with(['user', 'speciality'])
                ->when($request->speciality_id, fn($q) => $q->where('speciality_id', $request->speciality_id))
                // Has enough schedule slots covering the requested range
                ->whereHas(
                    'schedules',
                    fn($q) => $q
                        ->where('day_of_week', $dayOfWeek)
                        ->where('start_time', '>=', $startTime)
                        ->where('start_time', '<', $endTime),
                    '>=',
                    $slotsNeeded
                )
                // No overlapping non-cancelled appointment
                ->whereDoesntHave('appointments', fn($q) => $q
                    ->where('appointment_date', $date)
                    ->where('status', '!=', 'cancelado')
                    ->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime)
                )
                ->get();
        }

        return view('admin.appointments.create', compact(
            'specialities', 'patients', 'availableDoctors', 'durations'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id'       => 'required|exists:patients,id',
            'doctor_id'        => 'required|exists:doctors,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'start_time'       => 'required|date_format:H:i',
            'duration'         => 'required|integer|in:60,90,120',
            'notes'            => 'nullable|string|max:1000',
        ]);

        // Compute end_time from start + duration (cast to int – PHP 8.4 Carbon requires int|float)
        $data['end_time'] = Carbon::createFromFormat('H:i', $data['start_time'])
            ->addMinutes((int) $data['duration'])
            ->format('H:i');
        unset($data['duration']);

        if (Appointment::hasConflict($data['doctor_id'], $data['appointment_date'], $data['start_time'], $data['end_time'])) {
            return back()->withInput()->withErrors([
                'conflict' => 'El doctor ya tiene una cita en ese horario. Por favor elige otro horario.',
            ]);
        }

        if (Appointment::isOutsideSchedule($data['doctor_id'], $data['appointment_date'], $data['start_time'], $data['end_time'])) {
            return back()->withInput()->withErrors([
                'conflict' => 'El doctor no tiene disponibilidad registrada para ese día y horario.',
            ]);
        }

        Appointment::create($data);

        return redirect()->route('admin.appointments.index')->with('swal', [
            'icon'  => 'success',
            'title' => '¡Cita creada!',
            'text'  => 'La cita médica ha sido agendada correctamente.',
        ]);
    }

    public function edit(Appointment $appointment)
    {
        $appointment->load(['patient.user', 'doctor.user']);
        $patients  = Patient::with('user')->get();
        $doctors   = Doctor::with('user')->get();
        $durations = self::DURATIONS;

        return view('admin.appointments.edit', compact('appointment', 'patients', 'doctors', 'durations'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'patient_id'       => 'required|exists:patients,id',
            'doctor_id'        => 'required|exists:doctors,id',
            'appointment_date' => 'required|date',
            'start_time'       => 'required|date_format:H:i',
            'duration'         => 'required|integer|in:60,90,120',
            'status'           => 'required|in:programado,completado,cancelado',
            'notes'            => 'nullable|string|max:1000',
        ]);

        $data['end_time'] = Carbon::createFromFormat('H:i', $data['start_time'])
            ->addMinutes((int) $data['duration'])
            ->format('H:i');
        unset($data['duration']);

        if ($data['status'] !== 'cancelado') {
            if (Appointment::hasConflict($data['doctor_id'], $data['appointment_date'], $data['start_time'], $data['end_time'], $appointment->id)) {
                return back()->withInput()->withErrors(['conflict' => 'El doctor ya tiene una cita en ese horario.']);
            }
            if (Appointment::isOutsideSchedule($data['doctor_id'], $data['appointment_date'], $data['start_time'], $data['end_time'])) {
                return back()->withInput()->withErrors(['conflict' => 'El doctor no tiene disponibilidad para ese día y horario.']);
            }
        }

        $appointment->update($data);

        return redirect()->route('admin.appointments.index')->with('swal', [
            'icon'  => 'success',
            'title' => 'Cita actualizada',
            'text'  => 'La cita médica ha sido actualizada correctamente.',
        ]);
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()->route('admin.appointments.index')->with('swal', [
            'icon'  => 'success',
            'title' => 'Cita eliminada',
            'text'  => 'La cita médica ha sido eliminada.',
        ]);
    }
}
