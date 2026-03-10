<x-admin-layout title="Calendario | MediCitas" :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Calendario'],
    ]">

    {{-- FullCalendar v5 CSS --}}
    @push('scripts')
    @endpush

    @once
        @push('scripts')
        <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
        @endpush
    @endonce

    <style>
        /* Slots fuera de horario = fondo gris claro */
        .fc-timegrid-slot-lane { background: #fafafa; }
        /* Slots disponibles = fondo verde (sobrescrito por background event) */
        .schedule-bg { opacity: 0.85 !important; }
        /* Hover en citas */
        .fc-event { cursor: pointer; transition: opacity .15s; }
        .fc-event:hover { opacity: 0.85; }
        /* Header del calendario */
        .fc .fc-toolbar-title { font-size: 1.1rem; font-weight: 700; }
        /* Ocultar texto de background events */
        .fc-bg-event .fc-event-title { display: none; }
    </style>

    {{-- Toolbar: doctor selector + acciones --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
        {{-- Selector de doctor --}}
        <div class="flex items-center gap-3">
            <label class="text-sm font-semibold text-gray-600 whitespace-nowrap">Doctor:</label>
            <select id="doctor-select"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 min-w-[220px]">
                <option value="">— Selecciona un doctor —</option>
                @foreach ($doctors as $doctor)
                    <option value="{{ $doctor->id }}"
                            data-schedule-url="{{ route('admin.doctors.schedule.edit', $doctor) }}">
                        {{ $doctor->user->name }}
                        @if ($doctor->speciality)
                            — {{ $doctor->speciality->name ?? '' }}
                        @endif
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Acciones --}}
        <div class="flex items-center gap-2" id="doctor-actions" style="display:none!important">
            <a id="btn-schedule" href="#"
               class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-teal-500 text-teal-600 text-sm font-medium hover:bg-teal-50 transition">
                <i class="fa-solid fa-clock"></i> Gestionar horario
            </a>
            <a id="btn-new-apt" href="{{ route('admin.appointments.create') }}"
               class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition">
                <i class="fa-solid fa-plus"></i> Nueva cita
            </a>
        </div>
    </div>

    {{-- Leyenda --}}
    <div class="flex flex-wrap gap-4 mb-3 text-xs text-gray-600" id="legend" style="display:none!important">
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-sm bg-green-200 inline-block border border-green-300"></span> Disponible</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-sm bg-blue-500 inline-block"></span> Programado</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-sm bg-emerald-500 inline-block"></span> Completado</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-sm bg-red-500 inline-block"></span> Cancelado</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-sm bg-gray-100 inline-block border border-gray-300"></span> Sin disponibilidad</span>
    </div>

    {{-- Calendario --}}
    <x-wire-card>
        <div id="calendar-placeholder" class="flex flex-col items-center justify-center py-24 text-gray-400">
            <i class="fa-regular fa-calendar-days text-5xl mb-3"></i>
            <p class="text-base font-medium">Selecciona un doctor para ver su calendario</p>
            <p class="text-sm mt-1">Usa el selector de arriba para elegir al doctor</p>
        </div>
        <div id="calendar" class="hidden"></div>
    </x-wire-card>

    {{-- Modal de confirmación rápida de cita --}}
    <div id="apt-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 mx-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Nueva cita</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form id="quick-apt-form" action="{{ route('admin.appointments.store') }}" method="POST">
                @csrf
                <input type="hidden" name="doctor_id" id="modal-doctor-id">
                <input type="hidden" name="appointment_date" id="modal-date">
                <input type="hidden" name="start_time" id="modal-start">
                <input type="hidden" name="end_time" id="modal-end">

                <div class="space-y-4">
                    <div class="bg-blue-50 rounded-lg p-3 text-sm text-blue-800">
                        <i class="fa-solid fa-calendar mr-1"></i>
                        <span id="modal-info"></span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Paciente <span class="text-red-500">*</span></label>
                        <select name="patient_id" id="modal-patient" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Selecciona un paciente</option>
                            @foreach (\App\Models\Patient::with('user')->get() as $patient)
                                <option value="{{ $patient->id }}">{{ $patient->user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notas (opcional)</label>
                        <textarea name="notes" rows="2" maxlength="500"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Motivo de la consulta…"></textarea>
                    </div>
                </div>

                <div class="flex gap-3 mt-5">
                    <button type="button" onclick="closeModal()"
                            class="flex-1 border border-gray-300 text-gray-700 rounded-lg py-2 text-sm font-medium hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="flex-1 bg-blue-600 text-white rounded-lg py-2 text-sm font-medium hover:bg-blue-700 transition">
                        <i class="fa-solid fa-calendar-check mr-1"></i> Guardar cita
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- FullCalendar v5 scripts --}}
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.min.js'></script>

    <script>
        let calendar = null;
        let currentDoctorId = null;
        const eventsUrl = '{{ route('admin.calendar.events') }}';

        const doctorSelect  = document.getElementById('doctor-select');
        const calEl         = document.getElementById('calendar');
        const placeholder   = document.getElementById('calendar-placeholder');
        const doctorActions = document.getElementById('doctor-actions');
        const legend        = document.getElementById('legend');
        const btnSchedule   = document.getElementById('btn-schedule');
        const btnNewApt     = document.getElementById('btn-new-apt');

        // Stores doctor's schedule blocks for availability checking
        let doctorScheduleBlocks = [];

        function isWithinSchedule(date, startTime, endTime) {
            if (doctorScheduleBlocks.length === 0) return false;
            // FullCalendar day: 0=Sun,1=Mon...6=Sat
            const fcDay = date.getDay();
            const clickedStart = startTime;
            const clickedEnd   = endTime;
            return doctorScheduleBlocks.some(block => {
                return block.daysOfWeek.includes(fcDay) &&
                       block.startTime <= clickedStart &&
                       block.endTime   >= clickedEnd;
            });
        }

        function initCalendar(doctorId) {
            if (calendar) { calendar.destroy(); }
            doctorScheduleBlocks = [];

            calendar = new FullCalendar.Calendar(calEl, {
                locale: 'es',
                initialView: 'timeGridWeek',
                headerToolbar: {
                    left:   'prev,next today',
                    center: 'title',
                    right:  'dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: {
                    today:  'Hoy',
                    month:  'Mes',
                    week:   'Semana',
                    day:    'Día',
                },
                slotMinTime: '07:00:00',
                slotMaxTime: '21:00:00',
                slotDuration: '00:30:00',
                slotLabelInterval: '01:00',
                allDaySlot: false,
                height: 700,
                nowIndicator: true,
                eventMaxStack: 3,

                // Fetch events from server, store schedule blocks for click checking
                events: function (fetchInfo, successCallback, failureCallback) {
                    fetch(`${eventsUrl}?doctor_id=${doctorId}&start=${fetchInfo.startStr}&end=${fetchInfo.endStr}`)
                        .then(r => r.json())
                        .then(data => {
                            // Cache schedule blocks (background events)
                            doctorScheduleBlocks = data.filter(e => e.display === 'background' && e.daysOfWeek);
                            successCallback(data);
                        })
                        .catch(() => failureCallback());
                },

                // Click on a time slot → only open modal if within doctor's schedule
                dateClick: function (info) {
                    // In month view, just navigate to week view for that day
                    if (info.view.type === 'dayGridMonth') {
                        calendar.changeView('timeGridDay', info.date);
                        return;
                    }

                    const start   = info.date;
                    const end     = new Date(start.getTime() + 30 * 60 * 1000);
                    const startTm = formatTime(start);
                    const endTm   = formatTime(end);
                    const dateStr = info.dateStr.split('T')[0];

                    if (!isWithinSchedule(start, startTm, endTm)) {
                        // Show brief unavailable notice in the slot
                        showSlotHint(info.jsEvent, 'Sin disponibilidad · Configura el horario del doctor');
                        return;
                    }

                    openModal(doctorId, dateStr, startTm, endTm);
                },

                // Click on existing event → go to edit
                eventClick: function (info) {
                    if (info.event.display === 'background') return; // Ignore schedule bg clicks
                    if (info.event.url) {
                        info.jsEvent.preventDefault();
                        window.location.href = info.event.url;
                    }
                },

                // Tooltip on hover
                eventDidMount: function (info) {
                    if (info.event.display === 'background') return;
                    const props = info.event.extendedProps;
                    info.el.title = `${info.event.title}\nEstado: ${props.statusText || ''}\n${props.notes ? 'Nota: ' + props.notes : ''}`;
                },

                // Style events by status
                eventClassNames: function (arg) {
                    if (arg.event.display === 'background') return [];
                    const status = arg.event.extendedProps.status;
                    return ['apt-event', `apt-${status}`];
                },
            });

            calendar.render();
        }

        function showSlotHint(jsEvent, message) {
            const hint = document.createElement('div');
            hint.textContent = message;
            hint.style.cssText = `
                position:fixed; z-index:9999; padding:6px 12px;
                background:#1f2937; color:#fff; border-radius:8px;
                font-size:12px; pointer-events:none;
                left:${jsEvent.clientX + 10}px; top:${jsEvent.clientY - 30}px;
                transition: opacity .3s; opacity:1;
                white-space:nowrap; box-shadow:0 2px 8px rgba(0,0,0,.3);
            `;
            document.body.appendChild(hint);
            setTimeout(() => { hint.style.opacity = '0'; }, 1500);
            setTimeout(() => hint.remove(), 1900);
        }

        function formatTime(d) {
            return d.toTimeString().slice(0, 5); // HH:MM
        }

        function openModal(doctorId, date, start, end) {
            document.getElementById('modal-doctor-id').value = doctorId;
            document.getElementById('modal-date').value      = date;
            document.getElementById('modal-start').value     = start;
            document.getElementById('modal-end').value       = end;

            // Build readable label
            const [y, m, d] = date.split('-');
            const days = ['dom','lun','mar','mié','jue','vie','sáb'];
            const dayName = days[new Date(date + 'T12:00').getDay()];
            document.getElementById('modal-info').textContent =
                `${dayName} ${d}/${m}/${y} · ${start} – ${end}`;

            const modal = document.getElementById('apt-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            const modal = document.getElementById('apt-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Close modal on backdrop click
        document.getElementById('apt-modal').addEventListener('click', function (e) {
            if (e.target === this) closeModal();
        });

        // Doctor selector change
        doctorSelect.addEventListener('change', function () {
            const doctorId = this.value;
            const opt      = this.options[this.selectedIndex];

            if (!doctorId) {
                calEl.classList.add('hidden');
                placeholder.classList.remove('hidden');
                doctorActions.style.display = 'none !important';
                legend.style.display = 'none !important';
                return;
            }

            currentDoctorId = doctorId;

            // Update action buttons
            const scheduleUrl = opt.dataset.scheduleUrl;
            btnSchedule.href = scheduleUrl;
            btnNewApt.href   = `{{ route('admin.appointments.create') }}?doctor_id=${doctorId}`;

            doctorActions.removeAttribute('style');
            legend.removeAttribute('style');
            placeholder.classList.add('hidden');
            calEl.classList.remove('hidden');

            initCalendar(doctorId);
        });
    </script>

</x-admin-layout>
