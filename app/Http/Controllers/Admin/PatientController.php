<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodType;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

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
        $this->ensureBloodTypesExist();
        $bloodTypes = BloodType::pluck('name', 'id')->toArray();
        return view('admin.patients.edit', compact('patient', 'bloodTypes'));
    }

    /**
     * Asegura que la tabla blood_types tenga datos.
     */
    private function ensureBloodTypesExist(): void
    {
        if (BloodType::exists()) {
            return;
        }
        $names = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        foreach ($names as $name) {
            BloodType::firstOrCreate(['name' => $name]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, Patient $patient)
{
    // #region agent log
    $log = function (string $hypothesisId, string $location, string $message, array $data) {
        Log::info("[DEBUG_BLOOD] {$hypothesisId} {$location} {$message}", $data);
    };
    $log('A', 'PatientController@update:entry', 'request input', ['blood_type_id_raw' => $request->input('blood_type_id'), 'request_keys' => array_keys($request->all()), 'patient_id' => $patient->id]);
    // #endregion

    $this->ensureBloodTypesExist();

    // Normalizar blood_type_id: vacío/array/0 -> null; si viene id numérico, entero; si viene nombre (ej. "A+"), resolver a id
    $bloodTypeId = $request->input('blood_type_id');
    if (is_array($bloodTypeId)) {
        $bloodTypeId = $bloodTypeId[0] ?? null;
    }
    if ($bloodTypeId !== null && $bloodTypeId !== '' && (string) $bloodTypeId !== '0') {
        if (is_numeric($bloodTypeId)) {
            $bloodTypeId = (int) $bloodTypeId;
        } else {
            // El formulario envió el nombre del tipo (ej. "A+") en lugar del id: resolver a id
            $resolved = BloodType::where('name', $bloodTypeId)->first();
            $bloodTypeId = $resolved?->id;
        }
    } else {
        $bloodTypeId = null;
    }
    if ($bloodTypeId === 0) {
        $bloodTypeId = null;
    }
    // #region agent log
    $log('B', 'PatientController@update:after_normalize', 'blood_type_id after normalize', ['bloodTypeId' => $bloodTypeId, 'patient_id' => $patient->id]);
    // #endregion
    $request->merge(['blood_type_id' => $bloodTypeId]);

    $validIds = BloodType::pluck('id')->map(fn ($id) => (int) $id)->toArray();
    // Solo validar "in" cuando hay valor; null lo acepta nullable
    $bloodTypeRules = ['nullable'];
    if ($bloodTypeId !== null && ! empty($validIds)) {
        $bloodTypeRules[] = Rule::in($validIds);
    }
    $request->validate([
        'blood_type_id' => $bloodTypeRules,
        'allergies' => 'nullable|string|max:255',
        'chronic_diseases' => 'nullable|string|max:255',
        'surgery_history' => 'nullable|string|max:255',
        'family_history' => 'nullable|string|max:255',
        'observations' => 'nullable|string|max:255',
        'emergency_contact_name' => 'nullable|string|max:255',
        'emergency_contact_phone' => 'nullable|string|max:20',
        'emergency_relationship' => 'nullable|string|max:50',
    ]);

    $data = $request->only([
        'allergies',
        'chronic_diseases',
        'surgery_history',
        'family_history',
        'observations',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_relationship',
    ]);

    // Usar siempre el blood_type_id ya normalizado y validado (evita que se pierda al leer del request)
    $data['blood_type_id'] = $bloodTypeId;

    // Solo incluir atributos que están en fillable del modelo
    $data = array_intersect_key($data, array_flip($patient->getFillable()));

    // No enviar emergency_contact_name si la columna aún no existe (migración pendiente)
    if (array_key_exists('emergency_contact_name', $data) && ! Schema::hasColumn('patients', 'emergency_contact_name')) {
        unset($data['emergency_contact_name']);
    }

    // Normalizar: vacío -> null para campos opcionales
    $nullableKeys = ['blood_type_id', 'allergies', 'chronic_diseases', 'surgery_history', 'family_history', 'observations', 'emergency_contact_name', 'emergency_contact_phone', 'emergency_relationship'];
    foreach ($nullableKeys as $key) {
        if (isset($data[$key]) && $data[$key] === '') {
            $data[$key] = null;
        }
    }

    if (isset($data['emergency_contact_phone']) && $data['emergency_contact_phone'] !== null) {
        $data['emergency_contact_phone'] = preg_replace('/\D/', '', $data['emergency_contact_phone']);
    }

    $patient->update($data);

    // Persistir blood_type_id directamente en la BD (evita problemas con Eloquent/mass assignment)
    // #region agent log
    $log('C', 'PatientController@update:before_db_update', 'value passed to DB::table update', ['blood_type_id' => $bloodTypeId, 'patient_id' => $patient->id]);
    // #endregion
    $rowsAffected = DB::table('patients')
        ->where('id', $patient->id)
        ->update(['blood_type_id' => $bloodTypeId]);
    // #region agent log
    $log('D', 'PatientController@update:after_db_update', 'DB update result', ['rows_affected' => $rowsAffected, 'patient_id' => $patient->id]);
    $patient->refresh();
    $log('E', 'PatientController@update:after_refresh', 'patient after refresh', ['blood_type_id_in_model' => $patient->blood_type_id, 'patient_id' => $patient->id]);
    // #endregion

    session()->flash('swal', [
        'icon'  => 'success',
        'title' => 'Paciente actualizado',
        'text'  => 'La información del paciente ha sido actualizada exitosamente'
    ]);

    return redirect()->route('admin.patients.edit', $patient);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        // Eliminar el usuario (el paciente se eliminará en cascada)
        $patient->user->delete();

        session()->flash('swal',[
            'icon' => 'success',
            'title' => 'Paciente eliminado',
            'text' => 'El paciente ha sido eliminado exitosamente.'
        ]);

        return redirect()->route('admin.patients.index');
    }
}