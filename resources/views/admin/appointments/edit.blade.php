<x-admin-layout title="Citas | MediCitas" :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Citas', 'href' => route('admin.appointments.index')],
        ['name' => 'Editar #' . $appointment->id],
    ]">

    @if ($errors->has('conflict'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation"></i>
            {{ $errors->first('conflict') }}
        </div>
    @endif

    <form action="{{ route('admin.appointments.update', $appointment) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Cabecera --}}
        <x-wire-card class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-xl font-bold text-gray-900">Cita #{{ $appointment->id }}</p>
                    <p class="text-sm text-gray-500">
                        Paciente: {{ $appointment->patient->user->name }} |
                        Doctor: {{ $appointment->doctor->user->name }}
                    </p>
                </div>
                <div class="flex gap-3">
                    <x-wire-button outline gray href="{{ route('admin.appointments.index') }}">
                        Volver
                    </x-wire-button>
                    <x-wire-button type="submit">
                        <i class="fa-solid fa-check mr-2"></i> Guardar cambios
                    </x-wire-button>
                </div>
            </div>
        </x-wire-card>

        <x-wire-card>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Paciente --}}
                <div>
                    <x-wire-native-select label="Paciente" name="patient_id">
                        <option value="">Selecciona un paciente</option>
                        @foreach ($patients as $patient)
                            <option value="{{ $patient->id }}"
                                {{ old('patient_id', $appointment->patient_id) == $patient->id ? 'selected' : '' }}>
                                {{ $patient->user->name }}
                            </option>
                        @endforeach
                    </x-wire-native-select>
                    @error('patient_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Doctor --}}
                <div>
                    <x-wire-native-select label="Doctor" name="doctor_id">
                        <option value="">Selecciona un doctor</option>
                        @foreach ($doctors as $doctor)
                            <option value="{{ $doctor->id }}"
                                {{ old('doctor_id', $appointment->doctor_id) == $doctor->id ? 'selected' : '' }}>
                                {{ $doctor->user->name }}
                            </option>
                        @endforeach
                    </x-wire-native-select>
                    @error('doctor_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Fecha --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                    <input type="date" name="appointment_date"
                           value="{{ old('appointment_date', $appointment->appointment_date->format('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('appointment_date') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Estado --}}
                <div>
                    <x-wire-native-select label="Estado" name="status">
                        @foreach (['programado' => 'Programado', 'completado' => 'Completado', 'cancelado' => 'Cancelado'] as $val => $label)
                            <option value="{{ $val }}"
                                {{ old('status', $appointment->status) === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </x-wire-native-select>
                    @error('status') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Hora inicio --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hora inicio</label>
                    <select name="start_time"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @for ($h = 7; $h < 20; $h++)
                            @foreach (['00', '30'] as $m)
                                @php $t = sprintf('%02d:%s', $h, $m); @endphp
                                <option value="{{ $t }}"
                                    {{ old('start_time', substr($appointment->start_time, 0, 5)) === $t ? 'selected' : '' }}>
                                    {{ $t }}
                                </option>
                            @endforeach
                        @endfor
                    </select>
                    @error('start_time') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Duración --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Duración</label>
                    @php
                        $aptDurationMin = (strtotime(substr($appointment->end_time, 0, 5)) - strtotime(substr($appointment->start_time, 0, 5))) / 60;
                        $aptDurationMin = max(60, round($aptDurationMin / 60) * 60); // snap to nearest hour, min 1h
                    @endphp
                    <select name="duration"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach ($durations as $mins => $label)
                            <option value="{{ $mins }}"
                                {{ (int) old('duration', $aptDurationMin) === $mins ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('duration') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Notas --}}
                <div class="md:col-span-2">
                    <x-wire-textarea label="Notas (opcional)" name="notes" rows="3" maxlength="1000"
                        placeholder="Observaciones, motivo de cancelación…">{{ old('notes', $appointment->notes) }}</x-wire-textarea>
                </div>
            </div>
        </x-wire-card>
    </form>
</x-admin-layout>
