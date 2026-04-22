@extends('layouts.app', ['title' => 'Doctor Dashboard | MediQueue'])

@section('content')
    @php
        $activeDoctorSection = $activeDoctorSection ?? 'queue';
    @endphp

    <div class="dashboard-page grid lg:grid-cols-[280px_minmax(0,1fr)] gap-6 items-start">
        @include('dashboards.partials.sidebar')

        <div>
            <div class="flex flex-wrap items-start justify-between gap-6 mb-8">
                <div>
                    <p class="text-sm text-slate-500 font-bold">Live queue sync enabled</p>
                    <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-dark mt-2">Good morning, Dr. {{ auth()->user()->name }}</h1>
                    <p class="text-slate-500 mt-2">{{ $doctor->specialization->name }} | {{ $doctor->clinic->name }}</p>
                    <p class="text-xs text-accent font-bold mt-2 uppercase tracking-widest">Live queue sync enabled</p>
                </div>

        <div class="dashboard-card rounded-[2rem] p-6 min-w-[280px]">
            <p class="text-xs uppercase tracking-widest text-primary font-bold">Currently Serving</p>
            <h2 class="text-3xl font-black text-gradient mt-3" id="serving-token">--</h2>
            <p class="text-sm text-slate-600 mt-2" id="serving-patient">No patient called yet</p>
        </div>
    </div>

    @if ($activeDoctorSection === 'queue')
    <div class="grid md:grid-cols-3 gap-4 mb-8">
        <div class="dashboard-card rounded-3xl p-6">
            <div class="text-xs uppercase tracking-widest text-slate-500 font-bold">Waiting</div>
            <div class="text-4xl font-black text-gradient mt-3" id="waiting-count">0</div>
        </div>
        <div class="dashboard-card rounded-3xl p-6">
            <div class="text-xs uppercase tracking-widest text-slate-500 font-bold">Called / In Progress</div>
            <div class="text-4xl font-black text-gradient mt-3" id="called-count">0</div>
        </div>
        <div class="dashboard-card rounded-3xl p-6">
            <div class="text-xs uppercase tracking-widest text-slate-500 font-bold">Completed</div>
            <div class="text-4xl font-black text-gradient mt-3" id="completed-count">0</div>
        </div>
    </div>

    <div id="doctor-queue-board" class="grid xl:grid-cols-3 gap-6">
        <section class="dashboard-card rounded-[2rem] p-6">
            <h2 class="text-2xl font-extrabold text-dark mb-5">Waiting Queue</h2>
            <div class="space-y-4" id="waiting-column">
                <p class="text-slate-500">Loading queue...</p>
            </div>
        </section>

        <section class="dashboard-card rounded-[2rem] p-6">
            <h2 class="text-2xl font-extrabold text-dark mb-5">Called Now</h2>
            <div class="space-y-4" id="called-column">
                <p class="text-slate-500">No active patient.</p>
            </div>
        </section>

        <section class="dashboard-card rounded-[2rem] p-6">
            <h2 class="text-2xl font-extrabold text-dark mb-5">Completed Today</h2>
            <div class="space-y-4" id="completed-column">
                <p class="text-slate-500">No completed visits yet.</p>
            </div>
        </section>
    </div>
    @endif

    @if ($activeDoctorSection === 'prescriptions')
    <div id="doctor-prescriptions" class="mt-8 dashboard-card rounded-[2rem] p-8">
        <h2 class="text-2xl font-extrabold mb-5 text-dark">Prescription Desk</h2>
        <p class="text-sm text-slate-500 mb-6">Select a called/completed appointment from the queue board, open consultation if needed, and save/update the prescription below.</p>

        <form method="POST" action="" id="doctor-prescription-form" class="space-y-4">
            @csrf
            <select id="prescription-appointment-select" class="form-control" required>
                <option value="">Select appointment</option>
                @foreach ($appointments as $appointment)
                    <option value="{{ $appointment->id }}" data-action="{{ route('doctor.prescriptions.store', $appointment) }}">
                        Token #{{ $appointment->queueToken?->token_number }} - {{ $appointment->patient->name }}
                    </option>
                @endforeach
            </select>

            <div class="grid md:grid-cols-2 gap-4">
                <textarea name="diagnosis" rows="3" class="form-control" placeholder="Diagnosis" required></textarea>
                <textarea name="notes" rows="3" class="form-control" placeholder="Additional notes"></textarea>
            </div>

            <div class="grid md:grid-cols-4 gap-3">
                <input name="medicine_name[]" class="form-control" placeholder="Medicine" required>
                <input name="dosage[]" class="form-control" placeholder="Dosage" required>
                <input name="frequency[]" class="form-control" placeholder="Frequency" required>
                <input name="duration[]" class="form-control" placeholder="Duration" required>
            </div>

            <button class="bg-gradient-premium text-white px-6 py-3 rounded-2xl font-bold shadow-glow" type="submit">Save Prescription</button>
        </form>
    </div>
    @endif
        </div>
    </div>

    <script>
        const queueCsrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const liveDoctorAppointmentsUrl = "{{ route('doctor.appointments.live') }}";
        const waitingColumn = document.getElementById('waiting-column');
        const calledColumn = document.getElementById('called-column');
        const completedColumn = document.getElementById('completed-column');
        const waitingCount = document.getElementById('waiting-count');
        const calledCount = document.getElementById('called-count');
        const completedCount = document.getElementById('completed-count');
        const servingToken = document.getElementById('serving-token');
        const servingPatient = document.getElementById('serving-patient');
        const prescriptionSelect = document.getElementById('prescription-appointment-select');
        const prescriptionForm = document.getElementById('doctor-prescription-form');

        function escapeQueueHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function renderQueueCard(appointment, mode) {
            const canCall = mode === 'waiting';
            const canComplete = mode === 'called';
            const badgeClass = mode === 'waiting'
                ? 'bg-blue-50 text-primary'
                : (mode === 'called' ? 'bg-purple-50 text-secondary' : 'bg-green-50 text-accent');

            return `
                <div class="rounded-[1.5rem] border border-slate-100 p-5 bg-slate-50/60">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-xl font-black text-dark">#${escapeQueueHtml(appointment.token_number ?? '-')}</div>
                            <div class="text-sm font-bold text-dark mt-1">${escapeQueueHtml(appointment.patient_name)}</div>
                            <div class="text-xs text-slate-500 mt-1">${escapeQueueHtml(appointment.consultation_type)} | ${escapeQueueHtml(appointment.symptoms)}</div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold ${badgeClass}">${escapeQueueHtml(appointment.token_status || appointment.appointment_status)}</span>
                    </div>

                    <div class="flex flex-wrap gap-2 mt-4">
                        ${appointment.consultation_url ? `<a href="${escapeQueueHtml(appointment.consultation_url)}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-xs font-bold text-primary">Consultation</a>` : ''}
                        ${canCall ? `<button type="button" data-queue-action="${escapeQueueHtml(appointment.call_url)}" class="px-4 py-2 rounded-xl bg-gradient-premium text-white text-xs font-bold shadow-glow">Call Next</button>` : ''}
                        ${canComplete ? `<button type="button" data-queue-action="${escapeQueueHtml(appointment.complete_url)}" class="px-4 py-2 rounded-xl bg-dark text-white text-xs font-bold">Complete</button>` : ''}
                    </div>
                </div>
            `;
        }

        function renderColumn(node, rows, mode, emptyText) {
            if (!node) return;
            node.innerHTML = rows.length
                ? rows.map((appointment) => renderQueueCard(appointment, mode)).join('')
                : `<p class="text-slate-500">${emptyText}</p>`;
        }

        async function submitQueueAction(actionUrl) {
            const response = await fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': queueCsrfToken,
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                const data = await response.json();
                window.showToast(data.message || 'Queue updated successfully.', 'success');
                await refreshDoctorQueue();
            }
        }

        async function refreshDoctorQueue() {
            const response = await fetch(liveDoctorAppointmentsUrl, {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) return;

            const data = await response.json();
            const appointments = data.appointments || [];
            const waitingRows = appointments.filter((appointment) => appointment.token_status === 'waiting');
            const calledRows = appointments.filter((appointment) => appointment.token_status === 'called');
            const completedRows = appointments.filter((appointment) => appointment.token_status === 'completed');

            if (waitingCount) waitingCount.innerText = waitingRows.length;
            if (calledCount) calledCount.innerText = calledRows.length;
            if (completedCount) completedCount.innerText = completedRows.length;

            const activePatient = calledRows[0];
            if (servingToken) servingToken.innerText = activePatient ? `#${activePatient.token_number}` : '--';
            if (servingPatient) servingPatient.innerText = activePatient ? activePatient.patient_name : 'No patient called yet';

            renderColumn(waitingColumn, waitingRows, 'waiting', 'No waiting patients.');
            renderColumn(calledColumn, calledRows, 'called', 'No active patient.');
            renderColumn(completedColumn, completedRows, 'completed', 'No completed visits yet.');

            if (prescriptionSelect && !prescriptionSelect.value) {
                prescriptionSelect.innerHTML = '<option value="">Select appointment</option>' + appointments
                    .filter((appointment) => ['called', 'completed'].includes(appointment.token_status))
                    .map((appointment) => `
                        <option value="${appointment.id}" data-action="${escapeQueueHtml("{{ url('/doctor/appointments') }}/" + appointment.id + "/prescriptions")}">
                            Token #${escapeQueueHtml(appointment.token_number)} - ${escapeQueueHtml(appointment.patient_name)}
                        </option>
                    `).join('');
            }

            document.querySelectorAll('[data-queue-action]').forEach((button) => {
                button.addEventListener('click', () => submitQueueAction(button.dataset.queueAction));
            });
        }

        if (prescriptionSelect && prescriptionForm) {
            prescriptionSelect.addEventListener('change', () => {
                const selectedOption = prescriptionSelect.options[prescriptionSelect.selectedIndex];
                prescriptionForm.action = selectedOption?.dataset?.action || '';
            });
        }

        refreshDoctorQueue();
        setInterval(refreshDoctorQueue, 4000);
    </script>
@endsection
