<x-admin-layout
  title="Pacientes | MediCitas"
  :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Pacientes', 'href' => route('admin.patients.index')],
    ['name' => 'Editar']
  ]"
>

    <x-wire-card>
        <form action="{{ route('admin.patients.update', $patient) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-wire-select
                        label="Tipo de Sangre"
                        name="blood_type_id"
                        placeholder="Seleccione un tipo de sangre">
                        <option value="">Seleccione...</option>
                        @foreach($bloodTypes as $bloodType)
                            <option value="{{ $bloodType->id }}"
                                {{ old('blood_type_id', $patient->blood_type_id) == $bloodType->id ? 'selected' : '' }}>
                                {{ $bloodType->name }}
                            </option>
                        @endforeach
                    </x-wire-select>
                </div>

                <div>
                    <x-wire-input
                        label="Alergias"
                        name="allergies"
                        placeholder="Ej: Penicilina, Polen, etc."
                        value="{{ old('allergies', $patient->allergies) }}">
                    </x-wire-input>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <x-wire-input
                        label="Enfermedades Crónicas"
                        name="chronic_diseases"
                        placeholder="Ej: Diabetes, Hipertensión, etc."
                        value="{{ old('chronic_diseases', $patient->chronic_diseases) }}">
                    </x-wire-input>
                </div>

                <div>
                    <x-wire-input
                        label="Historial de Cirugías"
                        name="surgery_history"
                        placeholder="Cirugías previas"
                        value="{{ old('surgery_history', $patient->surgery_history) }}">
                    </x-wire-input>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <x-wire-input
                        label="Historial Familiar"
                        name="family_history"
                        placeholder="Enfermedades heredofamiliares"
                        value="{{ old('family_history', $patient->family_history) }}">
                    </x-wire-input>
                </div>

                <div>
                    <x-wire-input
                        label="Observaciones"
                        name="observations"
                        placeholder="Notas adicionales"
                        value="{{ old('observations', $patient->observations) }}">
                    </x-wire-input>
                </div>
            </div>

            <div class="border-t border-gray-200 mt-6 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Contacto de Emergencia</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-wire-input
                            label="Teléfono de Emergencia"
                            name="emergency_contact_phone"
                            placeholder="Ej: +1234567890"
                            value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}">
                        </x-wire-input>
                    </div>

                    <div>
                        <x-wire-input
                            label="Relación"
                            name="emergency_relationship"
                            placeholder="Ej: Madre, Esposo/a, etc."
                            value="{{ old('emergency_relationship', $patient->emergency_relationship) }}">
                        </x-wire-input>
                    </div>
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <x-wire-button type='submit' blue>Actualizar Paciente</x-wire-button>
            </div>
        </form>
    </x-wire-card>

</x-admin-layout>
