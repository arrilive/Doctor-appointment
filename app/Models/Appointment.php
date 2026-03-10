<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_date',
        'start_time',
        'end_time',
        'status',
        'notes',
    ];

    protected $casts = [
        'appointment_date' => 'date',
    ];

    public function consultation()
    {
        return $this->hasOne(Consultation::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Verifica si existe conflicto de horario para un doctor en una fecha y rango de horas.
     * Retorna true si hay conflicto.
     */
    public static function hasConflict(
        int $doctorId,
        string $date,
        string $startTime,
        string $endTime,
        ?int $excludeId = null
    ): bool {
        return self::where('doctor_id', $doctorId)
            ->where('appointment_date', $date)
            ->where('status', '!=', 'cancelado')
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->where(function ($q) use ($startTime, $endTime) {
                // Se solapa si: inicio_existente < fin_nueva AND fin_existente > inicio_nueva
                $q->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
            })
            ->exists();
    }

    /**
     * Verifica si el doctor tiene disponibilidad registrada para ese día y franja horaria.
     * Retorna true si el doctor NO tiene horario que cubra la cita.
     *
     * La disponibilidad se almacena en bloques de 30 min. Para una cita de 1h
     * se necesitan 2 bloques consecutivos: verificamos que existan suficientes
     * bloques dentro del rango solicitado en ese día de semana.
     */
    public static function isOutsideSchedule(
        int $doctorId,
        string $date,
        string $startTime,
        string $endTime
    ): bool {
        $dayOfWeek   = (int) \Carbon\Carbon::parse($date)->format('N') - 1; // 0=Lunes…6=Domingo
        $startTs     = strtotime($startTime);
        $endTs       = strtotime($endTime);
        $slotsNeeded = ($endTs - $startTs) / (30 * 60);

        $covered = DoctorSchedule::where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->where('start_time', '>=', $startTime)
            ->where('start_time', '<', $endTime)
            ->count();

        return $covered < $slotsNeeded;
    }
}
