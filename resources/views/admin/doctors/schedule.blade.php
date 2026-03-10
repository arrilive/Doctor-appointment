<x-admin-layout title="Horarios | MediCitas" :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Doctores', 'href' => route('admin.doctors.index')],
        ['name' => $doctor->user->name],
        ['name' => 'Disponibilidad'],
    ]">

    <style>
        .slot-btn {
            padding: 4px 6px;
            border-radius: 6px;
            font-size: 11px;
            font-family: monospace;
            border: 1.5px solid #e5e7eb;
            background: #f9fafb;
            color: #6b7280;
            cursor: pointer;
            transition: all .15s;
            width: 100%;
            text-align: center;
            user-select: none;
        }
        .slot-btn:hover { border-color: #93c5fd; background: #eff6ff; color: #1d4ed8; }
        .slot-btn.active {
            background: #dcfce7;
            border-color: #4ade80;
            color: #15803d;
            font-weight: 600;
        }
        .slot-btn.active:hover { background: #bbf7d0; border-color: #22c55e; }
        .day-col { min-width: 130px; }
        .hour-label {
            writing-mode: horizontal-tb;
            font-size: 11px;
            color: #9ca3af;
            font-family: monospace;
            padding-right: 8px;
            white-space: nowrap;
        }
        .hour-row { border-top: 1px solid #f3f4f6; }
        .hour-row:first-child { border-top: none; }
    </style>

    <form action="{{ route('admin.doctors.schedule.update', $doctor) }}" method="POST" id="schedule-form">
        @csrf
        @method('PUT')
        {{-- Hidden inputs populated by JS --}}
        <div id="hidden-slots"></div>

        <x-wire-card>
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5">
                <div class="flex items-center gap-3">
                    <div class="h-11 w-11 rounded-full bg-indigo-50 border border-indigo-200 flex items-center justify-center shrink-0">
                        <span class="text-indigo-600 font-bold text-sm">
                            {{ collect(explode(' ', $doctor->user->name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('') }}
                        </span>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">{{ $doctor->user->name }}</h2>
                        <p class="text-xs text-gray-500">
                            Haz clic en los bloques para marcar disponibilidad · Click derecho para seleccionar por hora
                        </p>
                    </div>
                </div>
                <div class="flex gap-2 shrink-0">
                    <x-wire-button outline gray href="{{ route('admin.doctors.index') }}">
                        Volver
                    </x-wire-button>
                    <button type="button" onclick="clearAll()"
                            class="px-3 py-1.5 text-sm border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition">
                        <i class="fa-solid fa-eraser mr-1"></i> Limpiar
                    </button>
                    <button type="submit" id="save-btn"
                            class="px-4 py-1.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar horario
                    </button>
                </div>
            </div>

            {{-- Leyenda --}}
            <div class="flex items-center gap-4 mb-4 text-xs text-gray-500">
                <span class="flex items-center gap-1">
                    <span class="w-4 h-4 rounded bg-green-100 border border-green-400 inline-block"></span> Disponible
                </span>
                <span class="flex items-center gap-1">
                    <span class="w-4 h-4 rounded bg-gray-100 border border-gray-300 inline-block"></span> No disponible
                </span>
                <span class="ml-auto text-gray-400">
                    <i class="fa-solid fa-circle-info mr-1"></i>
                    Click = toggling individual slot · "Todos" = selecciona/deselecciona toda la columna
                </span>
            </div>

            {{-- Grid --}}
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="w-full border-collapse text-xs" id="schedule-table">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="w-14 py-3 px-2 text-gray-400 font-medium"></th>
                            @foreach ($days as $dayIndex => $dayName)
                                <th class="day-col py-3 px-2 text-center">
                                    <div class="font-bold text-gray-700 uppercase text-xs tracking-wide mb-1">{{ $dayName }}</div>
                                    <button type="button"
                                            onclick="toggleDay({{ $dayIndex }})"
                                            class="text-xs px-2 py-0.5 rounded border border-gray-300 text-gray-500 hover:border-blue-400 hover:text-blue-600 hover:bg-blue-50 transition"
                                            id="toggle-day-{{ $dayIndex }}">
                                        Todos
                                    </button>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php $lastHour = null; @endphp
                        @foreach ($timeSlots as $slot)
                            @php
                                $currentHour = substr($slot['start'], 0, 2);
                                $isHourStart = $currentHour !== $lastHour;
                                $lastHour = $currentHour;
                            @endphp
                            <tr class="hour-row {{ $isHourStart ? 'border-t-2 border-gray-200' : '' }} hover:bg-gray-50/50">
                                <td class="py-1 px-2 text-right">
                                    @if ($isHourStart)
                                        <span class="hour-label font-semibold text-gray-500">{{ $slot['start'] }}</span>
                                    @else
                                        <span class="hour-label text-gray-300">:30</span>
                                    @endif
                                </td>
                                @foreach ($days as $dayIndex => $dayName)
                                    @php
                                        $isActive = isset($existing[$dayIndex][$slot['start']]);
                                    @endphp
                                    <td class="py-1 px-1.5 day-col">
                                        <button type="button"
                                                class="slot-btn {{ $isActive ? 'active' : '' }}"
                                                data-day="{{ $dayIndex }}"
                                                data-start="{{ $slot['start'] }}"
                                                data-end="{{ $slot['end'] }}"
                                                onclick="toggleSlot(this)">
                                            {{ $slot['start'] }}–{{ $slot['end'] }}
                                        </button>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Counter --}}
            <div class="mt-3 text-xs text-gray-400 flex justify-between">
                <span id="slot-counter">0 bloques seleccionados</span>
                <span>Cada bloque = 30 minutos</span>
            </div>
        </x-wire-card>
    </form>

@push('scripts')
<script>
    // Build initial state from PHP (active slots)
    let activeSlots = {};

    function getKey(day, start) {
        return `${day}_${start}`;
    }

    function toggleSlot(btn) {
        btn.classList.toggle('active');
        updateCounter();
        syncHiddenInputs();
    }

    function toggleDay(dayIndex) {
        const slots = document.querySelectorAll(`.slot-btn[data-day="${dayIndex}"]`);
        const allActive = [...slots].every(s => s.classList.contains('active'));
        slots.forEach(s => {
            if (allActive) s.classList.remove('active');
            else s.classList.add('active');
        });
        updateCounter();
        syncHiddenInputs();
    }

    function clearAll() {
        if (!confirm('¿Limpiar toda la disponibilidad de este doctor?')) return;
        document.querySelectorAll('.slot-btn').forEach(s => s.classList.remove('active'));
        updateCounter();
        syncHiddenInputs();
    }

    function updateCounter() {
        const count = document.querySelectorAll('.slot-btn.active').length;
        const el = document.getElementById('slot-counter');
        el.textContent = `${count} bloque${count !== 1 ? 's' : ''} seleccionado${count !== 1 ? 's' : ''} (${(count * 30 / 60).toFixed(1)} h/semana aprox.)`;
        el.className = count > 0 ? 'text-xs text-green-600 font-medium' : 'text-xs text-gray-400';
    }

    function syncHiddenInputs() {
        const container = document.getElementById('hidden-slots');
        container.innerHTML = '';
        document.querySelectorAll('.slot-btn.active').forEach(btn => {
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = 'slots[]';
            input.value = `${btn.dataset.day}_${btn.dataset.start}_${btn.dataset.end}`;
            container.appendChild(input);
        });
    }

    // Loading spinner on save
    document.getElementById('schedule-form').addEventListener('submit', function() {
        const btn = document.getElementById('save-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Guardando…';
        syncHiddenInputs(); // ensure latest state
    });

    // Init on load
    updateCounter();
    syncHiddenInputs();
</script>
@endpush

</x-admin-layout>
