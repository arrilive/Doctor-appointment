<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\Prescription;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    /**
     * Show the consultation form for a given appointment.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load([
            'patient.user',
            'patient.bloodType',
            'doctor.user',
            'consultation.prescriptions',
        ]);

        // Previous consultations of the same patient (excluding this appointment)
        $previousConsultations = Appointment::with(['consultation', 'doctor.user'])
            ->where('patient_id', $appointment->patient_id)
            ->where('id', '!=', $appointment->id)
            ->whereHas('consultation')
            ->orderByDesc('appointment_date')
            ->limit(10)
            ->get();

        return view('admin.consultations.show', compact('appointment', 'previousConsultations'));
    }

    /**
     * Save (create or update) the consultation + its prescriptions.
     */
    public function store(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'diagnosis'               => 'nullable|string|max:5000',
            'treatment'               => 'nullable|string|max:5000',
            'notes'                   => 'nullable|string|max:5000',
            'status'                  => 'required|in:programado,completado,cancelado',
            'prescriptions'           => 'nullable|array',
            'prescriptions.*.medication' => 'required_with:prescriptions|string|max:255',
            'prescriptions.*.dosage'     => 'required_with:prescriptions|string|max:255',
            'prescriptions.*.frequency'  => 'nullable|string|max:255',
        ]);

        // Upsert consultation
        $consultation = $appointment->consultation ?? new Consultation(['appointment_id' => $appointment->id]);
        $consultation->fill([
            'appointment_id' => $appointment->id,
            'diagnosis'      => $data['diagnosis'] ?? null,
            'treatment'      => $data['treatment'] ?? null,
            'notes'          => $data['notes'] ?? null,
        ]);
        $consultation->save();

        // Sync prescriptions (delete old, insert new)
        $consultation->prescriptions()->delete();
        if (!empty($data['prescriptions'])) {
            foreach ($data['prescriptions'] as $rx) {
                $consultation->prescriptions()->create([
                    'medication' => $rx['medication'],
                    'dosage'     => $rx['dosage'],
                    'frequency'  => $rx['frequency'] ?? null,
                ]);
            }
        }

        // Update appointment status from the form select
        $appointment->update(['status' => $data['status']]);

        return redirect()
            ->route('admin.consultations.show', $appointment)
            ->with('swal', [
                'icon'  => 'success',
                'title' => '¡Consulta guardada!',
                'text'  => 'Los datos de la consulta han sido guardados correctamente.',
            ]);
    }
}
