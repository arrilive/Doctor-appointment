<x-admin-layout title="Citas | MediCitas" :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Citas', 'href' => route('admin.appointments.index')],
        ['name' => 'Nuevo'],
    ]">

    @if ($errors->has('conflict'))
        <div class="mb-4 p-4 bg-red-50 border border-red-300 rounded-lg text-red-700 text-sm flex items-start gap-2">
            <i class="fa-solid fa-circle-exclamation mt-0.5 shrink-0"></i>
            <span>{{ $errors->first('conflict') }}</span>
        </div>
    @endif

    {{-- ───── SEARCH FORM ───── --}}
    <x-wire-card class="mb-6">
        <h2 class="text-base font-bold text-gray-900 mb-1 flex items-center gap-2">
            <i class="fa-solid fa-magnifying-glass text-blue-500"></i> Buscar disponibilidad
        </h2>
        <p class="text-xs text-gray-500 mb-4">Elige el día, hora de inicio y duración para ver los doctores disponibles.</p>

        <form method="GET" action="{{ route('admin.appointments.create') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            {{-- Fecha --}}
            <div class="lg:col-span-1">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Fecha <span class="text-red-500">*</span></label>
                <input type="date" name="date"
                       value="{{ request('date', now()->format('Y-m-d')) }}"
                       min="{{ now()->format('Y-m-d') }}"
                       required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Hora inicio --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Hora inicio <span class="text-red-500">*</span></label>
                <select name="start_time" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @for ($h = 7; $h < 20; $h++)
                        @foreach (['00', '30'] as $m)
                            @php $t = sprintf('%02d:%s', $h, $m); @endphp
                            <option value="{{ $t }}" {{ request('start_time', '08:00') === $t ? 'selected' : '' }}>
                                {{ $t }}
                            </option>
                        @endforeach
                    @endfor
                </select>
            </div>

            {{-- Duración --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Duración <span class="text-red-500">*</span></label>
                <select name="duration" required
                        id="duration-select"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach ($durations as $mins => $label)
                        <option value="{{ $mins }}" {{ (int) request('duration', 60) === $mins ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Especialidad --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Especialidad (opcional)</label>
                <select name="speciality_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todas las especialidades</option>
                    @foreach ($specialities as $sp)
                        <option value="{{ $sp->id }}" {{ request('speciality_id') == $sp->id ? 'selected' : '' }}>
                            {{ $sp->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Submit --}}
            <div>
                <button type="submit"
                        class="w-full bg-blue-600 text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-blue-700 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-search"></i> Buscar
                </button>
            </div>
        </form>
    </x-wire-card>

    {{-- ───── SEARCH RESULTS ───── --}}
    @if (request()->filled('date'))
        @php
            $searchDate  = request('date');
            $searchStart = request('start_time', '08:00');
            $searchDur   = (int) request('duration', 60);
            $searchEnd   = \Carbon\Carbon::createFromFormat('H:i', $searchStart)->addMinutes($searchDur)->format('H:i');
            $dayLabel    = \Carbon\Carbon::parse($searchDate)->translatedFormat('l j \d\e F \d\e Y');
        @endphp

        <x-wire-card class="mb-6">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-user-doctor text-teal-500"></i>
                Doctores disponibles el {{ $dayLabel }} de {{ $searchStart }} a {{ $searchEnd }}
            </h3>

            @if ($availableDoctors->isEmpty())
                <div class="flex flex-col items-center justify-center py-10 text-gray-400">
                    <i class="fa-solid fa-calendar-xmark text-4xl mb-3"></i>
                    <p class="font-medium text-gray-500">Sin disponibilidad en ese horario</p>
                    <p class="text-sm mt-1">Prueba otra fecha, hora o duración, o configura la disponibilidad del doctor.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($availableDoctors as $doctor)
                        <button type="button"
                                onclick="selectDoctor({{ $doctor->id }}, '{{ e($doctor->user->name) }}')"
                                class="doctor-card text-left border-2 border-gray-200 rounded-xl p-4 hover:border-blue-400 hover:bg-blue-50 transition group"
                                id="doc-card-{{ $doctor->id }}">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-indigo-100 border border-indigo-200 flex items-center justify-center shrink-0">
                                    <span class="text-indigo-700 font-bold text-sm">
                                        {{ collect(explode(' ', $doctor->user->name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('') }}
                                    </span>
                                </div>
                                <div class="min-w-0">
                                    <div class="font-semibold text-gray-900 text-sm truncate">{{ $doctor->user->name }}</div>
                                    @if ($doctor->speciality)
                                        <div class="text-xs text-gray-500">{{ $doctor->speciality->name }}</div>
                                    @endif
                                </div>
                                <i class="fa-solid fa-circle-check text-green-500 ml-auto opacity-0 group-hover:opacity-100 selected-icon transition"></i>
                            </div>
                        </button>
                    @endforeach
                </div>
            @endif
        </x-wire-card>
    @endif

    {{-- ───── CONFIRM FORM ───── --}}
    <x-wire-card>
        <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-calendar-check text-blue-500"></i> Confirmar cita
        </h3>

        <form action="{{ route('admin.appointments.store') }}" method="POST">
            @csrf
            <input type="hidden" name="doctor_id" id="doctor-id-field" value="{{ old('doctor_id') }}">
            <input type="hidden" name="start_time" id="start-time-field"
                   value="{{ old('start_time', request('start_time', '08:00')) }}">
            <input type="hidden" name="duration" id="duration-field"
                   value="{{ old('duration', request('duration', 60)) }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Paciente --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Paciente <span class="text-red-500">*</span></label>
                    <select name="patient_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('patient_id') border-red-400 @enderror">
                        <option value="">Selecciona un paciente</option>
                        @foreach ($patients as $patient)
                            <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                {{ $patient->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Doctor seleccionado (display) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Doctor <span class="text-red-500">*</span></label>
                    <div id="doctor-display"
                         class="border border-dashed border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-400 bg-gray-50">
                        Selecciona un doctor arriba
                    </div>
                </div>

                {{-- Fecha --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha <span class="text-red-500">*</span></label>
                    <input type="date" name="appointment_date"
                           value="{{ old('appointment_date', request('date', now()->format('Y-m-d'))) }}"
                           min="{{ now()->format('Y-m-d') }}"
                           required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Hora + Duración (informativo) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hora de inicio</label>
                    <div class="border border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-sm text-gray-700 font-mono"
                         id="confirm-start">{{ request('start_time', '08:00') }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Duración</label>
                    <div class="border border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-sm text-gray-700"
                         id="confirm-duration">{{ $durations[(int) request('duration', 60)] ?? '1 hora' }}</div>
                </div>

                {{-- Notas --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notas (opcional)</label>
                    <textarea name="notes" rows="3" maxlength="500"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Motivo de la consulta, síntomas, observaciones…">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="flex gap-3 mt-5 justify-end">
                <a href="{{ route('admin.appointments.index') }}"
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition flex items-center gap-2">
                    <i class="fa-solid fa-calendar-plus"></i> Guardar cita
                </button>
            </div>
        </form>
    </x-wire-card>

@push('scripts')
<script>
    let selectedDoctorId   = null;
    const durationLabels = @json($durations);

    function selectDoctor(id, name) {
        // Deselect all
        document.querySelectorAll('.doctor-card').forEach(c => {
            c.classList.remove('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-300');
            c.querySelector('.selected-icon').classList.add('opacity-0');
            c.querySelector('.selected-icon').classList.remove('opacity-100');
        });

        // Select clicked
        const card = document.getElementById('doc-card-' + id);
        card.classList.add('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-300');
        card.querySelector('.selected-icon').classList.remove('opacity-0');
        card.querySelector('.selected-icon').classList.add('opacity-100');

        selectedDoctorId = id;
        document.getElementById('doctor-id-field').value  = id;
        document.getElementById('doctor-display').textContent = name;
        document.getElementById('doctor-display').classList.remove('text-gray-400', 'border-dashed');
        document.getElementById('doctor-display').classList.add('text-gray-900', 'border-blue-300', 'bg-blue-50');
    }

    // Sync confirm info when search form values change
    const startSelect    = document.querySelector('[name="start_time"]');
    const durSelect      = document.querySelector('[name="duration"]');
    const confirmStart   = document.getElementById('confirm-start');
    const confirmDur     = document.getElementById('confirm-duration');
    const sfStart        = document.getElementById('start-time-field');
    const sfDur          = document.getElementById('duration-field');

    function syncConfirm() {
        if (startSelect && confirmStart) {
            confirmStart.textContent = startSelect.value;
            sfStart.value = startSelect.value;
        }
        if (durSelect && confirmDur) {
            confirmDur.textContent = durationLabels[durSelect.value] || durSelect.value + ' min';
            sfDur.value = durSelect.value;
        }
    }

    if (startSelect) startSelect.addEventListener('change', syncConfirm);
    if (durSelect)   durSelect.addEventListener('change', syncConfirm);
</script>
@endpush

</x-admin-layout>
