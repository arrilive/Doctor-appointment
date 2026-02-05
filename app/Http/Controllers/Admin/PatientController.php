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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.patients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|unique:patients,user_id',
            'blood_type_id' => 'nullable|exists:blood_types,id',
            'allergies' => 'nullable|string|max:255',
            'chronic_diseases' => 'nullable|string|max:255',
            'surgery_history' => 'nullable|string|max:255',
            'family_history' => 'nullable|string|max:255',
            'observations' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_relationship' => 'nullable|string|max:100',
        ]);

        Patient::create($request->all());

        return redirect()
            ->route('admin.patients.index')
            ->with('swal', [
                'icon' => 'success',
                'title' => 'Paciente creado correctamente',
                'text' => 'El paciente ha sido creado exitosamente',
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        return view('admin.patients.show', compact('patient'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        $bloodTypes = BloodType::all();
        return view('admin.patients.edit', compact('patient', 'bloodTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'blood_type_id' => 'nullable|exists:blood_types,id',
            'allergies' => 'nullable|string|max:255',
            'chronic_diseases' => 'nullable|string|max:255',
            'surgery_history' => 'nullable|string|max:255',
            'family_history' => 'nullable|string|max:255',
            'observations' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_relationship' => 'nullable|string|max:100',
        ]);

        // Detectar si hay cambios
        $sinCambios = true;
        foreach (['blood_type_id', 'allergies', 'chronic_diseases', 'surgery_history',
                  'family_history', 'observations', 'emergency_contact_phone', 'emergency_relationship'] as $field) {
            if ($patient->$field != $request->$field) {
                $sinCambios = false;
                break;
            }
        }

        if ($sinCambios) {
            return redirect()
                ->route('admin.patients.index')
                ->with('swal', [
                    'icon' => 'info',
                    'title' => 'Sin cambios',
                    'text' => 'No se detectaron modificaciones.',
                ]);
        }

        $patient->update($request->only([
            'blood_type_id',
            'allergies',
            'chronic_diseases',
            'surgery_history',
            'family_history',
            'observations',
            'emergency_contact_phone',
            'emergency_relationship'
        ]));

        return redirect()
            ->route('admin.patients.index')
            ->with('swal', [
                'icon' => 'success',
                'title' => 'Paciente actualizado correctamente',
                'text' => 'El paciente ha sido actualizado exitosamente.',
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        // Solo admins pueden eliminar pacientes
        if (! auth()->user()->can('access-admin')) {
            abort(403, 'No tienes permisos para eliminar pacientes.');
        }

        $patient->delete();

        return redirect()
            ->route('admin.patients.index')
            ->with('swal', [
                'icon' => 'success',
                'title' => 'Paciente eliminado',
                'text' => 'El paciente ha sido eliminado correctamente.',
            ]);
    }
}
