<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\BloodType;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.patients.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        $patient->load(['user', 'bloodType']);
        return view('admin.patients.show', compact('patient'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        $bloodTypes = BloodType::all();
        $patient->load(['user', 'bloodType']);

        return view('admin.patients.edit', compact('patient', 'bloodTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        // Validar primero (antes de sanitizar) para dar errores correctos
        $data = $request->validate([
            'blood_type_id'            => 'nullable|exists:blood_types,id',
            'allergies'                => 'nullable|string|max:1000',
            'chronic_diseases'         => 'nullable|string|max:1000',
            'surgery_history'          => 'nullable|string|max:1000',
            'family_history'           => 'nullable|string|max:1000',
            'observations'             => 'nullable|string|max:1000',
            'emergency_contact_name'   => 'nullable|string|max:255',
            'emergency_contact_phone'  => ['nullable', 'string', 'regex:/^[0-9\s\(\)\-]+$/', function ($attribute, $value, $fail) {
                $digits = preg_replace('/[^0-9]/', '', $value);
                if (strlen($digits) !== 10) {
                    $fail('El teléfono de contacto debe tener exactamente 10 dígitos.');
                }
            }],
            'emergency_relationship'   => 'nullable|string|max:50',
        ]);

        // Sanitizar el teléfono después de validar
        if (!empty($data['emergency_contact_phone'])) {
            $data['emergency_contact_phone'] = preg_replace('/[^0-9]/', '', $data['emergency_contact_phone']);
        }

        $patient->fill($data);

        if (!$patient->isDirty()) {
            return redirect()
                ->back()
                ->with('swal', [
                    'icon'  => 'info',
                    'title' => 'Sin cambios',
                    'text'  => 'No se detectaron cambios en el expediente.',
                ]);
        }

        $patient->save();

        return redirect()
            ->route('admin.patients.edit', $patient)
            ->with('swal', [
                'icon'  => 'success',
                'title' => 'Expediente actualizado',
                'text'  => 'El expediente médico ha sido actualizado exitosamente.',
            ]);
    }
}