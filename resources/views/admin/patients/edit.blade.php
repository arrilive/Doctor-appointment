<x-admin-layout
    title="Editar Información Médica | MediCitas"
    :breadcrumbs="[
        [
            'name' => 'Dashboard',
            'href' => route('admin.dashboard'),
        ],
        [
            'name' => 'Pacientes',
            'href' => route('admin.patients.index'),
        ],
        [
            'name' => 'Editar',
        ],
    ]">
        <form action="{{ route('admin.patients.update', $patient) }}" method="POST"
              onsubmit="var s=document.querySelector('[name=blood_type_id_ui]');if(s) document.getElementById('blood_type_id_submit').value=s.value||''; return true;">
            @csrf
            @method('PUT')
            {{-- Siempre enviar tipo de sangre (el select puede estar en tab oculto y no enviarse en algunos navegadores) --}}
            <input type="hidden" name="blood_type_id" id="blood_type_id_submit" value="{{ old('blood_type_id', $patient->blood_type_id) }}">
            {{-- Encabezado con foto y acciones--}}
        <x-wire-card class="mb-4">
            <div class="lg:flex lg:justify-between lg:items-center">
                <div>
                    <div class="flex items-center">
                        <img src="{{ $patient->user->profile_photo_url }}" alt="{{ $patient->user->name }}"
                        class="w-20 h-20 rounded-full object-cover object-center">
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-gray-900">{{ $patient->user->name}}</p>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-3 mt-6 lg:mt-0">
                    <x-wire-button outline href="{{ route('admin.patients.index') }}">Volver</x-wire-button>
                    <x-wire-button type="submit">
                        <i class="fa-solid fa-check"></i>
                        Guardar Cambios
                    </x-wire-button>
                </div>
            </div>
        </x-wire-card>

        {{-- Tabs de navegación --}}
        <x-wire-card>
            @php
                // Determinar qué tab debe estar activo basado en los errores
                $activeTab = 'datos-personales';
                
                if ($errors->has('allergies') || $errors->has('chronic_diseases') ||
                    $errors->has('surgery_history') || $errors->has('family_history')) {
                    $activeTab = 'antecedentes';
                } elseif ($errors->has('blood_type_id') || $errors->has('observations')) {
                    $activeTab = 'informacion-general';
                } elseif ($errors->has('emergency_contact_name') || $errors->has('emergency_contact_phone') ||
                          $errors->has('emergency_relationship')) {
                    $activeTab = 'contactos-emergencia';
                }
            @endphp

            <div x-data="{ tab: '{{ $activeTab }}' }">

                {{-- Menú de pestañas --}}
                <div class="border-b border-gray-200">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500">

                        {{-- Tab 1: Datos Personales --}}
                        <li class="me-2">
                            <a href="#" x-on:click="tab = 'datos-personales'"
                            :class="{
                                'text-blue-600 border-blue-600 active': tab === 'datos-personales',
                                'border-transparent hover:border-gray-300': tab !== 'datos-personales'
                            }"
                               class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group transition-colors duration-200"
                               :aria-current="tab === 'datos-personales' ? 'page' : undefined">
                               <i class="fa-solid fa-user me-2"></i>
                                Datos Personales
                            </a>
                        </li>
                        {{-- Tab 2: Antecedentes --}}
                        <li class="me-2">
                            <a href="#" x-on:click="tab = 'antecedentes'"
                            :class="{
                                'text-blue-600 border-blue-600 active': tab === 'antecedentes',
                                'border-transparent hover:border-gray-300': tab !== 'antecedentes'
                            }"
                               class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group transition-colors duration-200"
                               :aria-current="tab === 'antecedentes' ? 'page' : undefined">
                               <i class="fa-solid fa-file-lines me-2"></i>
                                Antecedentes
                            </a>
                        </li>
                        {{-- Tab 3: Informacion general --}}
                        <li class="me-2">
                            <a href="#" x-on:click="tab = 'informacion-general'"
                            :class="{
                                'text-blue-600 border-blue-600 active': tab === 'informacion-general',
                                'border-transparent hover:border-gray-300': tab !== 'informacion-general'
                            }"
                               class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group transition-colors duration-200"
                               :aria-current="tab === 'informacion-general' ? 'page' : undefined">
                               <i class="fa-solid fa-info me-2"></i>
                                Informacion general
                            </a>
                        </li>
                         {{-- Tab 4: Contactos de emergencia --}}
                        <li class="me-2">
                            <a href="#" x-on:click="tab = 'contactos-emergencia'"
                            :class="{
                                'text-blue-600 border-blue-600 active': tab === 'contactos-emergencia',
                                'border-transparent hover:border-gray-300': tab !== 'contactos-emergencia'
                            }"
                               class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group transition-colors duration-200"
                               :aria-current="tab === 'contactos-emergencia' ? 'page' : undefined">
                               <i class="fa-solid fa-heart me-2"></i>
                                Contactos de emergencia
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Contenido de los tabs --}}
                <div class="px-4 mt-4">
                    {{-- Contenido del tab1: Datos Personales --}}
                    <div x-show="tab === 'datos-personales'">
                        <div class="bg-blue-100 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg shadow-sm">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                {{-- Lado izquierdo: Información --}}
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-user-cog text-blue-500 text-xl mt-1"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-bold text-blue-800">
                                            Edición de cuenta de usuario
                                        </h3>
                                        <p class="mt-1 text-sm text-blue-600">
                                            La <strong>información de acceso</strong> del paciente se muestra a continuación
                                            (Nombre, email y contraseña). Debe gestionarse desde la cuenta de usuario asociado.
                                        </p>
                                    </div>
                                </div>
                                {{-- Lado derecho: Botón de acción --}}
                                <div class="flex-shrink-0">
                                    <x-wire-button primary smn href="{{ route('admin.users.edit', $patient->user_id) }}"
                                    target="_blank">
                                        <i class="fa-solid fa-pen-to-square me-2"></i>
                                        Editar cuenta de usuario
                                    </x-wire-button>
                                </div>
                            </div>
                        </div>
                        <div class="grid lg:grid-cols-2 gap-4">
                            <div>
                                <span class="text-gray-500 font-semibold ml-1">Telefono:</span>
                                <span class="text-gray-500 font-semibold ml-1">{{$patient->user->phone}}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 font-semibold ml-1">Email:</span>
                                <span class="text-gray-500 font-semibold ml-1">{{$patient->user->email}}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 font-semibold ml-1">Direccion:</span>
                                <span class="text-gray-500 font-semibold ml-1">{{$patient->user->address}}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Contenido del tab2: Antecedentes --}}
                    <div x-show="tab == 'antecedentes'" style="display: none;">
                        <div class="grid lg:grid-cols-2 gap-4">
                            <div>
                                <x-wire-text 
                                    label="Alergias conocidas" 
                                    name="allergies" 
                                    placeholder="Mariscos, penicilina, etc."
                                    value="{{ old('allergies', $patient->allergies) }}" 
                                />
                            </div>
                            <div>
                                <x-wire-text 
                                    label="Enfermedades crónicas" 
                                    name="chronic_diseases" 
                                    value="{{ old('chronic_diseases', $patient->chronic_diseases) }}" 
                                />
                            </div>
                            <div>
                                <x-wire-text 
                                    label="Antecedentes familiares" 
                                    name="family_history" 
                                    value="{{ old('family_history', $patient->family_history) }}" 
                                />
                            </div>
                            <div>
                                <x-wire-text 
                                    label="Antecedentes quirúrgicos" 
                                    name="surgery_history" 
                                    value="{{ old('surgery_history', $patient->surgery_history) }}" 
                                />
                            </div>
                        </div>
                    </div>

                    {{-- Contenido del tab3: Informacion general --}}
                    <div x-show="tab == 'informacion-general'" style="display: none;">
                        <div class="grid lg:grid-cols-2 gap-4">
                            <x-wire-native-select
                                label="Tipo de sangre"
                                name="blood_type_id_ui"
                                :options="$bloodTypes"
                                :value="old('blood_type_id', $patient->blood_type_id)"
                                placeholder="Selecciona un tipo de sangre"
                                class="mb-4"
                                onchange="document.getElementById('blood_type_id_submit').value=this.value"
                            />
                            <x-wire-textarea
                                label="Observaciones"
                                name="observations"
                                value="{{ old('observations', $patient->observations) }}"
                            />
                        </div>
                    </div>

                    {{-- Contenido del tab4: Contactos de emergencia --}}
                    <div x-show="tab == 'contactos-emergencia'" style="display: none;">
                        <div class="space-y-4">
                            <x-wire-input 
                                label="Nombre de contacto" 
                                name="emergency_contact_name"
                                value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}"
                            />
                            <x-wire-phone-input 
                                label="Teléfono de contacto" 
                                name="emergency_contact_phone"
                                mask="(###) ###-####"
                                placeholder="(999) 999-9999"
                                value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}"
                            />
                            <x-wire-input 
                                label="Relación con el contacto" 
                                name="emergency_relationship"
                                placeholder="Hermano, padre, madre, etc."
                                value="{{ old('emergency_relationship', $patient->emergency_relationship) }}"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </x-wire-card>
        </form>
    </x-admin-layout>