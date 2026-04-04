@extends('layouts.app', ['title' => 'Appointment Details | MediQueue'])

@section('content')
    <div class="grid lg:grid-cols-3 gap-6">
        <section class="lg:col-span-2 bg-white rounded-[2rem] border border-slate-100 p-8 shadow-soft">
            <p class="text-sm uppercase tracking-widest text-primary font-bold">Live Appointment</p>
            <h1 class="text-4xl font-extrabold tracking-tight text-dark mt-2">Dr. {{ $appointment->doctor->user->name }}</h1>
            <p class="text-slate-500 mt-2">{{ $appointment->doctor->specialization->name }} | {{ $appointment->clinic->name }}, {{ $appointment->clinic->area->name }}</p>

            <div class="grid md:grid-cols-3 gap-4 mt-8">
                <div class="rounded-3xl bg-blue-50 p-5">
                    <div class="text-sm text-slate-500">Token Number</div>
                    <div id="token-number" class="text-4xl font-black text-primary">#{{ $appointment->queueToken?->token_number }}</div>
                </div>
                <div class="rounded-3xl bg-purple-50 p-5">
                    <div class="text-sm text-slate-500">Queue Position</div>
                    <div id="queue-position" class="text-4xl font-black text-secondary">{{ $queuePosition }}</div>
                </div>
                <div class="rounded-3xl bg-green-50 p-5">
                    <div class="text-sm text-slate-500">Estimated Wait</div>
                    <div id="wait-time" class="text-4xl font-black text-accent" data-eta-seconds="{{ $queuePosition * ($appointment->doctor->avg_consultation_minutes ?? 15) * 60 }}">
                        {{ $queuePosition * ($appointment->doctor->avg_consultation_minutes ?? 15) }}m 00s
                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4 mt-6">
                <div class="rounded-3xl bg-slate-50 p-5">
                    <div class="text-sm text-slate-500">Currently Serving Token</div>
                    <div id="serving-token" class="text-3xl font-black text-secondary mt-1">--</div>
                </div>
                <div class="rounded-3xl bg-slate-50 p-5">
                    <div class="flex items-center justify-between text-sm text-slate-500">
                        <span>Queue Progress</span>
                        <span id="queue-progress-label">10%</span>
                    </div>
                    <div class="w-full h-4 bg-white rounded-full overflow-hidden mt-3">
                        <div id="queue-progress-bar" class="h-full bg-gradient-premium rounded-full transition-all duration-500" style="width: 10%;"></div>
                    </div>
                </div>
            </div>

            <div class="mt-8 space-y-3 text-slate-700">
                <div><strong>Date:</strong> {{ $appointment->appointment_date->format('d M Y') }}</div>
                <div><strong>Type:</strong> {{ ucfirst($appointment->consultation_type) }}</div>
                <div><strong>Status:</strong> <span id="appointment-status" class="font-semibold text-primary">{{ ucfirst(str_replace('_', ' ', $appointment->status)) }}</span></div>
                <div><strong>Symptoms:</strong> {{ $appointment->symptoms ?: 'Not provided' }}</div>
            </div>

            @if ($appointment->consultation)
                <a href="{{ route('consultations.show', $appointment->consultation) }}" class="inline-flex mt-8 bg-gradient-premium text-white px-6 py-3 rounded-2xl font-bold shadow-glow">Open Online Consultation</a>
            @endif
        </section>

        <aside class="glass-panel rounded-[2rem] border border-white/70 p-8 shadow-glass">
            <h2 class="text-2xl font-extrabold text-dark mb-5">Prescription</h2>
            @if ($appointment->prescription)
                <p class="text-sm text-slate-500">Issued {{ $appointment->prescription->issued_at?->format('d M Y, h:i A') }}</p>
                <div class="mt-4">
                    <div class="text-sm text-slate-500">Diagnosis</div>
                    <div class="font-semibold text-dark">{{ $appointment->prescription->diagnosis }}</div>
                </div>
                <div class="mt-5 space-y-3">
                    @foreach ($appointment->prescription->items as $item)
                        <div class="rounded-2xl bg-white p-4 shadow-sm border border-slate-100">
                            <div class="font-bold text-dark">{{ $item->medicine_name }}</div>
                            <div class="text-sm text-slate-600">{{ $item->dosage }} | {{ $item->frequency }} | {{ $item->duration }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="grid sm:grid-cols-2 gap-3 mt-6">
                    <a href="{{ route('prescriptions.download', $appointment->prescription) }}" class="block text-center w-full border border-primary/20 text-primary py-3 rounded-2xl font-bold hover:bg-primary hover:text-white transition-all">Download PDF</a>
                    <a href="{{ route('prescriptions.image', $appointment->prescription) }}" class="block text-center w-full border border-secondary/20 text-secondary py-3 rounded-2xl font-bold hover:bg-secondary hover:text-white transition-all">Download Image</a>
                </div>
            @else
                <p class="text-slate-500">Prescription will appear here after doctor consultation.</p>
            @endif
        </aside>
    </div>

    <script>
        function formatAppointmentEta(totalSeconds) {
            const safeSeconds = Math.max(0, Number(totalSeconds || 0));
            const minutes = Math.floor(safeSeconds / 60);
            const seconds = String(safeSeconds % 60).padStart(2, '0');

            return `${minutes}m ${seconds}s`;
        }

        function tickAppointmentEta() {
            const waitNode = document.getElementById('wait-time');
            if (!waitNode) return;

            const nextSeconds = Math.max(0, Number(waitNode.dataset.etaSeconds || 0) - 1);
            waitNode.dataset.etaSeconds = String(nextSeconds);
            waitNode.innerText = formatAppointmentEta(nextSeconds);
        }

        async function refreshQueue() {
            const response = await fetch("{{ route('appointments.status', $appointment) }}", {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();
            document.getElementById('token-number').innerText = `#${data.token_number ?? '-'}`;
            document.getElementById('queue-position').innerText = data.queue_position;
            document.getElementById('wait-time').dataset.etaSeconds = data.eta_seconds;
            document.getElementById('wait-time').innerText = formatAppointmentEta(data.eta_seconds);
            document.getElementById('serving-token').innerText = `#${data.currently_serving_token ?? '--'}`;
            document.getElementById('queue-progress-label').innerText = `${data.progress_percent}%`;
            document.getElementById('queue-progress-bar').style.width = `${data.progress_percent}%`;
            document.getElementById('appointment-status').innerText = data.appointment_status.replaceAll('_', ' ');
        }

        refreshQueue();
        setInterval(tickAppointmentEta, 1000);
        setInterval(refreshQueue, 5000);
    </script>
@endsection
