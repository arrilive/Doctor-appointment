<x-admin-layout title="Consulta | MediCitas" :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Citas', 'href' => route('admin.appointments.index')],
        ['name' => 'Consulta #' . $appointment->id],
    ]">

    <style>
        .tab-btn { padding: 10px 20px; border-bottom: 3px solid transparent; font-weight: 500; font-size: 14px; color: #6b7280; cursor: pointer; transition: all .15s; white-space: nowrap; }
        .tab-btn.active { border-color: #3b82f6; color: #1d4ed8; }
        .tab-btn:hover:not(.active) { color: #374151; border-color: #e5e7eb; }
        .tab-panel { display: none; }
        .tab-panel.active { display: block; }
        .rx-row { display: grid; grid-template-columns: 2fr 1fr 1.5fr 36px; gap: 8px; align-items: center; }
        .rx-row input { width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 7px 10px; font-size: 13px; }
        .rx-row input:focus { outline: none; border-color: #60a5fa; box-shadow: 0 0 0 3px rgba(59,130,246,.15); }
    </style>

    <form action="{{ route('admin.consultations.store', $appointment) }}" method="POST" id="consultation-form">
    @csrf

    {{-- ─── PACIENTE HEADER ─── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <div class="flex items-center gap-3">
            <div class="h-12 w-12 rounded-full bg-blue-100 border-2 border-blue-200 flex items-center justify-center shrink-0 text-blue-700 font-bold text-lg">
                {{ collect(explode(' ', $appointment->patient->user->name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('') }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $appointment->patient->user->name }}</h2>
                <div class="flex items-center gap-3 text-xs text-gray-500 mt-0.5">
                    <span><i class="fa-solid fa-id-card mr-1"></i> {{ $appointment->patient->user->id_number ?? 'N/A' }}</span>
                    <span><i class="fa-solid fa-calendar mr-1"></i> {{ \Carbon\Carbon::parse($appointment->appointment_date)->translatedFormat('d M Y') }}</span>
                    <span><i class="fa-solid fa-clock mr-1"></i> {{ substr($appointment->start_time, 0, 5) }} – {{ substr($appointment->end_time, 0, 5) }}</span>
                    <span>Dr. {{ $appointment->doctor->user->name }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            {{-- Ver Historial --}}
            <button type="button" onclick="openModal('history-modal')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-300 text-gray-600 text-sm hover:bg-gray-50 transition">
                <i class="fa-solid fa-file-medical text-indigo-500"></i> Ver Historial
            </button>
            {{-- Consultas Anteriores --}}
            <button type="button" onclick="openModal('prev-modal')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-300 text-gray-600 text-sm hover:bg-gray-50 transition">
                <i class="fa-solid fa-clock-rotate-left text-teal-500"></i> Consultas Anteriores
            </button>
            {{-- Estado de la cita --}}
            @php
                $statusColors = [
                    'programado' => 'text-blue-700 bg-blue-50 border-blue-300',
                    'completado' => 'text-green-700 bg-green-50 border-green-300',
                    'cancelado'  => 'text-red-700 bg-red-50 border-red-300',
                ];
                $currentStatus = old('status', $appointment->status);
            @endphp
            <select name="status" id="status-select"
                    class="text-sm border rounded-lg px-2.5 py-1.5 font-medium focus:outline-none focus:ring-2 focus:ring-blue-400 transition {{ $statusColors[$currentStatus] ?? 'text-gray-700 bg-white border-gray-300' }}">
                <option value="programado" {{ $currentStatus === 'programado' ? 'selected' : '' }}>Programado</option>
                <option value="completado" {{ $currentStatus === 'completado' ? 'selected' : '' }}>Completado</option>
                <option value="cancelado"  {{ $currentStatus === 'cancelado'  ? 'selected' : '' }}>Cancelado</option>
            </select>
            {{-- Guardar --}}
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition">
                <i class="fa-solid fa-floppy-disk"></i> Guardar Consulta
            </button>
        </div>
    </div>

    {{-- Success / Error flash --}}
    @if (session('swal'))
        <div class="mb-4 p-3 bg-green-50 border border-green-300 text-green-700 text-sm rounded-lg flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i> {{ session('swal')['text'] }}
        </div>
    @endif

    {{-- ─── TABS ─── --}}
    <x-wire-card>
        <div class="flex border-b border-gray-200 mb-5 -mx-1">
            <button type="button" class="tab-btn active" onclick="switchTab('tab-consulta', this)">
                <i class="fa-solid fa-stethoscope mr-1.5 text-blue-500"></i> Consulta
            </button>
            <button type="button" class="tab-btn" onclick="switchTab('tab-receta', this)">
                <i class="fa-solid fa-prescription-bottle-medical mr-1.5 text-green-500"></i> Receta
            </button>
        </div>

        {{-- ── TAB: CONSULTA ── --}}
        <div id="tab-consulta" class="tab-panel active space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Diagnóstico</label>
                <textarea name="diagnosis" rows="4" maxlength="5000"
                          placeholder="Describe el diagnóstico del paciente aquí…"
                          class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 resize-y">{{ old('diagnosis', $appointment->consultation?->diagnosis) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tratamiento</label>
                <textarea name="treatment" rows="3" maxlength="5000"
                          placeholder="Describe el tratamiento recomendado aquí…"
                          class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 resize-y">{{ old('treatment', $appointment->consultation?->treatment) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Notas adicionales</label>
                <textarea name="notes" rows="2" maxlength="5000"
                          placeholder="Agrega notas adicionales sobre la consulta…"
                          class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 resize-y">{{ old('notes', $appointment->consultation?->notes) }}</textarea>
            </div>
        </div>

        {{-- ── TAB: RECETA ── --}}
        <div id="tab-receta" class="tab-panel">
            <div class="rx-row mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wide px-1">
                <span>Medicamento</span><span>Dosis</span><span>Frecuencia / Duración</span><span></span>
            </div>

            <div id="rx-list" class="space-y-2">
                {{-- Existing prescriptions --}}
                @if ($appointment->consultation && $appointment->consultation->prescriptions->isNotEmpty())
                    @foreach ($appointment->consultation->prescriptions as $rx)
                        <div class="rx-row" id="rx-row-{{ $loop->index }}">
                            <input type="text" name="prescriptions[{{ $loop->index }}][medication]"
                                   value="{{ old("prescriptions.{$loop->index}.medication", $rx->medication) }}"
                                   placeholder="Ej. Amoxicilina 500mg" required>
                            <input type="text" name="prescriptions[{{ $loop->index }}][dosage]"
                                   value="{{ old("prescriptions.{$loop->index}.dosage", $rx->dosage) }}"
                                   placeholder="1 cada 8 horas" required>
                            <input type="text" name="prescriptions[{{ $loop->index }}][frequency]"
                                   value="{{ old("prescriptions.{$loop->index}.frequency", $rx->frequency) }}"
                                   placeholder="Ej. 7 días">
                            <button type="button" onclick="removeRx(this)"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition shrink-0">
                                <i class="fa-solid fa-xmark text-sm"></i>
                            </button>
                        </div>
                    @endforeach
                @else
                    {{-- Empty row --}}
                    <div class="rx-row" id="rx-row-0">
                        <input type="text" name="prescriptions[0][medication]" placeholder="Ej. Amoxicilina 500mg" required>
                        <input type="text" name="prescriptions[0][dosage]" placeholder="1 cada 8 horas" required>
                        <input type="text" name="prescriptions[0][frequency]" placeholder="Ej. 7 días">
                        <button type="button" onclick="removeRx(this)"
                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition shrink-0">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>
                @endif
            </div>

            <button type="button" onclick="addRx()"
                    class="mt-4 inline-flex items-center gap-2 px-4 py-2 border-2 border-dashed border-gray-300 rounded-xl text-sm text-gray-500 hover:border-blue-400 hover:text-blue-600 transition">
                <i class="fa-solid fa-plus"></i> Añadir Medicamento
            </button>

            <p class="text-xs text-gray-400 mt-3">
                <i class="fa-solid fa-circle-info mr-1"></i>
                La receta se guarda junto a la consulta al hacer clic en "Guardar Consulta".
            </p>
        </div>
    </x-wire-card>

    </form>


    {{-- ════════════════════════════════════════════
         MODAL: HISTORIA MÉDICA
    ════════════════════════════════════════════ --}}
    <div id="history-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                    <i class="fa-solid fa-file-medical text-indigo-500"></i> Historia médica del paciente
                </h3>
                <button onclick="closeModal('history-modal')" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <div class="px-6 py-5">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Tipo de sangre</span>
                        <p class="text-sm font-medium text-gray-900 mt-0.5">
                            {{ $appointment->patient->bloodType?->name ?? 'No registrado' }}
                        </p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Alergias</span>
                        <p class="text-sm text-gray-700 mt-0.5">{{ $appointment->patient->allergies ?: 'No registradas' }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Enfermedades crónicas</span>
                        <p class="text-sm text-gray-700 mt-0.5">{{ $appointment->patient->chronic_diseases ?: 'No registradas' }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Antecedentes quirúrgicos</span>
                        <p class="text-sm text-gray-700 mt-0.5">{{ $appointment->patient->surgery_history ?: 'No registrados' }}</p>
                    </div>
                </div>
                @if ($appointment->patient->family_history)
                    <div class="mb-4">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Historia familiar</span>
                        <p class="text-sm text-gray-700 mt-0.5">{{ $appointment->patient->family_history }}</p>
                    </div>
                @endif
                @if ($appointment->patient->observations)
                    <div class="mb-4">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Observaciones</span>
                        <p class="text-sm text-gray-700 mt-0.5">{{ $appointment->patient->observations }}</p>
                    </div>
                @endif
            </div>
            <div class="px-6 py-3 border-t border-gray-100 flex justify-between items-center">
                <a href="{{ route('admin.patients.show', $appointment->patient) }}"
                   class="text-sm text-blue-600 hover:underline flex items-center gap-1">
                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i> Ver / Editar Historia Médica
                </a>
                <button onclick="closeModal('history-modal')"
                        class="px-4 py-1.5 bg-gray-100 rounded-lg text-sm text-gray-600 hover:bg-gray-200 transition">
                    Cerrar
                </button>
            </div>
        </div>
    </div>


    {{-- ════════════════════════════════════════════
         MODAL: CONSULTAS ANTERIORES
    ════════════════════════════════════════════ --}}
    <div id="prev-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden max-h-[85vh] flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-teal-500"></i> Consultas Anteriores
                    <span class="ml-2 text-xs bg-gray-100 text-gray-600 rounded-full px-2 py-0.5">{{ $previousConsultations->count() }}</span>
                </h3>
                <button onclick="closeModal('prev-modal')" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="overflow-y-auto flex-1 px-6 py-4 space-y-3">
                @forelse ($previousConsultations as $prev)
                    <div class="border border-gray-200 rounded-xl p-4 hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="fa-regular fa-calendar text-gray-400 text-xs"></i>
                                    <span class="text-xs font-semibold text-gray-600">
                                        {{ \Carbon\Carbon::parse($prev->appointment_date)->translatedFormat('d/m/Y') }}
                                        a las {{ substr($prev->start_time, 0, 5) }}
                                    </span>
                                    <span class="text-xs text-gray-400">·</span>
                                    <span class="text-xs text-gray-500">Dr. {{ $prev->doctor?->user?->name }}</span>
                                </div>
                                @if ($prev->consultation?->diagnosis)
                                    <p class="text-sm text-gray-700">
                                        <span class="font-semibold text-gray-800">Diagnóstico:</span>
                                        {{ \Str::limit($prev->consultation->diagnosis, 100) }}
                                    </p>
                                @endif
                                @if ($prev->consultation?->treatment)
                                    <p class="text-sm text-gray-600 mt-1">
                                        <span class="font-semibold">Tratamiento:</span>
                                        {{ \Str::limit($prev->consultation->treatment, 80) }}
                                    </p>
                                @endif
                                @if ($prev->consultation?->notes)
                                    <p class="text-xs text-gray-500 mt-1">
                                        <span class="font-semibold">Notas:</span>
                                        {{ \Str::limit($prev->consultation->notes, 80) }}
                                    </p>
                                @endif
                            </div>
                            <a href="{{ route('admin.consultations.show', $prev) }}"
                               class="shrink-0 text-xs px-3 py-1.5 border border-blue-300 text-blue-600 rounded-lg hover:bg-blue-50 transition whitespace-nowrap">
                                Consultar Detalle
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-400 text-sm">
                        <i class="fa-solid fa-file-circle-xmark text-3xl mb-2 block"></i>
                        Este paciente no tiene consultas anteriores registradas.
                    </div>
                @endforelse
            </div>
            <div class="px-6 py-3 border-t border-gray-100 text-right">
                <button onclick="closeModal('prev-modal')"
                        class="px-4 py-1.5 bg-gray-100 rounded-lg text-sm text-gray-600 hover:bg-gray-200 transition">
                    Cerrar
                </button>
            </div>
        </div>
    </div>


@push('scripts')
<script>
    // ── Tabs ──
    function switchTab(id, btn) {
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById(id).classList.add('active');
        btn.classList.add('active');
    }

    // ── Modals ──
    function openModal(id) {
        const m = document.getElementById(id);
        m.classList.remove('hidden');
        m.classList.add('flex');
    }
    function closeModal(id) {
        const m = document.getElementById(id);
        m.classList.add('hidden');
        m.classList.remove('flex');
    }
    // Close on backdrop click
    ['history-modal', 'prev-modal'].forEach(id => {
        document.getElementById(id).addEventListener('click', function(e) {
            if (e.target === this) closeModal(id);
        });
    });

    // ── Prescription rows ──
    let rxIndex = {{ $appointment->consultation?->prescriptions->count() ?? 1 }};

    function addRx() {
        const list = document.getElementById('rx-list');
        const div  = document.createElement('div');
        div.className = 'rx-row';
        div.innerHTML = `
            <input type="text" name="prescriptions[${rxIndex}][medication]" placeholder="Ej. Amoxicilina 500mg" required>
            <input type="text" name="prescriptions[${rxIndex}][dosage]" placeholder="1 cada 8 horas" required>
            <input type="text" name="prescriptions[${rxIndex}][frequency]" placeholder="Ej. 7 días">
            <button type="button" onclick="removeRx(this)"
                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition shrink-0">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        `;
        list.appendChild(div);
        rxIndex++;
        div.querySelector('input').focus();
    }

    function removeRx(btn) {
        const row = btn.closest('.rx-row');
        if (document.querySelectorAll('#rx-list .rx-row').length <= 1) {
            // Clear instead of remove if it's the last row
            row.querySelectorAll('input').forEach(i => i.value = '');
        } else {
            row.remove();
        }
    }
</script>
@endpush

</x-admin-layout>
