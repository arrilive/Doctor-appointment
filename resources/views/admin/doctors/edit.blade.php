<x-admin-layout title="Doctores | MediCitas" :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Doctores', 'href' => route('admin.doctors.index')],
        ['name' => 'Editar'],
    ]">
    <form action="{{ route('admin.doctors.update', $doctor) }}" method="POST">
        @csrf
        @method('PUT')
        
        <x-wire-card class="mt-10 mb-6">
            <div class="lg:flex lg:justify-between lg:items-center">
                <div class="flex items-center gap-4">
                    {{-- Avatar con iniciales o foto si hubiese --}}
                    @if($doctor->user->profile_photo_url && !str_contains($doctor->user->profile_photo_url, 'ui-avatars.com'))
                        <img src="{{ $doctor->user->profile_photo_url }}" alt="{{ $doctor->user->name }}"
                             class="h-16 w-16 rounded-full object-cover object-center">
                    @else
                        <div class="h-16 w-16 rounded-full bg-indigo-50 border border-indigo-100 flex items-center justify-center">
                            <span class="text-indigo-600 font-bold text-xl">
                                {{ collect(explode(' ', $doctor->user->name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('') }}
                            </span>
                        </div>
                    @endif
                    <div>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $doctor->user->name }}
                        </p>
                        <p class="text-sm text-gray-500">
                            Licencia: {{ $doctor->medical_license_number ?: 'N/A' }} | Biografía: {{ $doctor->biography ?: 'N/A' }}
                        </p>
                    </div>
                </div>
                <div class="flex space-x-3 mt-6 lg:mt-0">
                    <x-wire-button outline gray href="{{ route('admin.doctors.index') }}">
                        Volver
                    </x-wire-button>
                    <x-wire-button type="submit">
                        <i class="fa-solid fa-check mr-2"></i>
                        Guardar cambios
                    </x-wire-button>
                </div>
            </div>
        </x-wire-card>

        <x-wire-card>
            <div class="space-y-6">
                {{-- Especialidad (Native Select de WireUI) --}}
                <div>
                    <x-wire-native-select label="Especialidad" name="speciality_id">
                        <option value="">Seleccione una especialidad</option>
                        @foreach ($specialities as $speciality)
                            <option value="{{ $speciality->id }}" @selected(old('speciality_id', $doctor->speciality_id) == $speciality->id)>
                                {{ $speciality->name }}
                            </option>
                        @endforeach
                    </x-wire-native-select>
                </div>
                
                {{-- Número de licencia médica --}}
                <div>
                    <x-wire-input label="Número de licencia médica" name="medical_license_number"
                        minlength="7" maxlength="15" title="La licencia médica debe tener entre 7 y 15 caracteres"
                        placeholder="Entre 7 y 15 caracteres"
                        value="{{ old('medical_license_number', $doctor->medical_license_number) }}" />
                </div>
                
                {{-- Biografía --}}
                <div>
                    <x-wire-textarea label="Biografía" name="biography" rows="4" maxlength="1000" placeholder="Máximo 1000 caracteres">{{ old('biography', $doctor->biography) }}</x-wire-textarea>
                </div>
            </div>
        </x-wire-card>

    </form>
</x-admin-layout>
