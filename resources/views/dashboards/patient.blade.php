@extends('layouts.app', ['title' => 'Patient Dashboard | MediQueue'])

@section('content')
    @php
        $activePatientSection = $activePatientSection ?? 'appointments';
    @endphp

    <div class="grid lg:grid-cols-[280px_minmax(0,1fr)] gap-6 items-start">
        @include('dashboards.partials.sidebar')

        <div>
            <div class="flex flex-wrap items-center justify-between gap-4 mb-10">
                <div>
                    <p class="text-sm uppercase tracking-widest text-primary font-bold">Patient Dashboard</p>
                    <h1 class="text-5xl font-extrabold tracking-tight text-dark mt-2">Hello, {{ auth()->user()->name }}</h1>
                </div>
                <a href="{{ route('clinics.index') }}" class="bg-dark text-white px-6 py-3 rounded-full font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all">Book New Token</a>
        </div>

    <div class="grid xl:grid-cols-3 gap-6">
        @if ($activePatientSection === 'appointments')
        <section id="patient-appointments" class="xl:col-span-2 bg-white rounded-[2rem] border border-slate-100 p-8 shadow-soft">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-extrabold text-dark">Live Queue & Appointments</h2>
                    <p class="text-sm text-slate-500 mt-1">Track queue position, countdown ETA, queue progress, and prescription downloads in real time.</p>
                </div>
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-50 text-emerald-600 text-xs font-bold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    Live Sync
                </span>
            </div>
            <div class="space-y-4" id="patient-appointments-list">
                @forelse ($appointments as $appointment)
                    <div class="premium-card rounded-[2rem] border border-slate-100 p-5 bg-gradient-to-br from-white to-slate-50">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="flex items-start gap-4">
                                <div class="w-14 h-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center text-2xl shrink-0">
                                    <i class="ph-fill ph-ticket"></i>
                                </div>
                                <div>
                                    <div class="text-lg font-bold text-dark">Dr. {{ $appointment->doctor->user->name }}</div>
                                    <div class="text-slate-500 text-sm">{{ $appointment->doctor->specialization->name }} at {{ $appointment->clinic->name }}</div>
                                    <div class="text-xs text-slate-400 mt-1">{{ $appointment->appointment_date->format('d M Y') }} | {{ ucfirst($appointment->consultation_type) }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs uppercase tracking-wide text-slate-400">Token</div>
                                <div class="text-4xl font-black text-gradient">#{{ $appointment->queueToken?->token_number ?? '-' }}</div>
                                <div class="text-xs font-bold uppercase text-accent">{{ str_replace('_', ' ', $appointment->status) }}</div>
                            </div>
                        </div>

                        <div class="grid sm:grid-cols-4 gap-3 mt-5">
                            <div class="rounded-3xl bg-blue-50 p-4">
                                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-bold">Queue Position</p>
                                <p class="text-2xl font-black text-primary mt-1">Syncing</p>
                            </div>
                            <div class="rounded-3xl bg-purple-50 p-4">
                                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-bold">Now Serving</p>
                                <p class="text-2xl font-black text-secondary mt-1">--</p>
                            </div>
                            <div class="rounded-3xl bg-green-50 p-4">
                                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-bold">ETA Timer</p>
                                <p class="text-2xl font-black text-accent mt-1">Syncing</p>
                            </div>
                            <div class="rounded-3xl bg-slate-50 p-4">
                                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-bold">Progress</p>
                                <div class="w-full h-2.5 bg-white rounded-full overflow-hidden mt-3">
                                    <div class="h-full bg-gradient-premium rounded-full" style="width: 5%;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3 mt-5">
                            <a href="{{ route('appointments.show', $appointment) }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-dark text-white text-sm font-bold hover:bg-slate-800 transition-all">
                                Open Queue Tracker
                                <i class="ph-bold ph-arrow-right"></i>
                            </a>
                            @if ($appointment->prescription)
                                <a href="{{ route('prescriptions.download', $appointment->prescription) }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-primary/10 text-primary text-sm font-bold hover:bg-primary hover:text-white transition-all">
                                    <i class="ph-bold ph-file-pdf"></i>
                                    PDF
                                </a>
                                <a href="{{ route('prescriptions.image', $appointment->prescription) }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-secondary/10 text-secondary text-sm font-bold hover:bg-secondary hover:text-white transition-all">
                                    <i class="ph-bold ph-image-square"></i>
                                    Image
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500">No appointments yet. Visit Clinics and book your first token.</p>
                @endforelse
            </div>
        </section>
        @endif

        @if ($activePatientSection === 'ai-tools')
        <section class="space-y-6 xl:col-span-2">
            <div id="patient-ai" class="glass-panel rounded-[2rem] p-6 shadow-glass">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-secondary flex items-center justify-center text-xl">
                        <i class="ph-fill ph-sparkle"></i>
                    </div>
                    <h2 class="text-xl font-extrabold text-dark">AI Symptom Checker</h2>
                </div>
                <form id="symptom-form" class="space-y-4">
                    <textarea name="symptoms" rows="4" class="form-control" placeholder="Example: fever and cough for two days"></textarea>
                    <button class="w-full bg-gradient-premium text-white rounded-2xl py-3 font-bold shadow-glow" type="submit">Suggest Doctor Type</button>
                </form>
                <div id="symptom-result" class="mt-4 text-sm text-slate-700"></div>
            </div>

            <div id="patient-chatbot" class="glass-panel rounded-[2rem] p-6 shadow-glass">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-primary flex items-center justify-center text-xl">
                        <i class="ph-fill ph-chat-circle-text"></i>
                    </div>
                    <h2 class="text-xl font-extrabold text-dark">AI Chatbot Assistant</h2>
                </div>
                <form id="chatbot-form" class="space-y-4">
                    <input name="message" class="form-control" placeholder="Ask about booking or queue">
                    <button class="w-full border border-primary/20 text-primary rounded-2xl py-3 font-bold hover:bg-primary hover:text-white transition-all" type="submit">Ask Assistant</button>
                </form>
                <div id="chatbot-reply" class="mt-4 text-sm text-slate-700"></div>
            </div>
        </section>
        @endif

        @if ($activePatientSection === 'reviews')
        <section class="xl:col-span-2">
            <div id="patient-platform-reviews" class="bg-white rounded-[2rem] p-6 shadow-soft border border-slate-100">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl">
                        <i class="ph-fill ph-star-half"></i>
                    </div>
                    <h2 class="text-xl font-extrabold text-dark">Rate MediQueue</h2>
                </div>

                <form method="POST" action="{{ route('platform.reviews.store') }}" class="space-y-3">
                    @csrf
                    <select name="rating" class="form-control" required>
                        @foreach ([5, 4, 3, 2, 1] as $score)
                            <option value="{{ $score }}" @selected((int) ($myPlatformReview?->rating ?? 5) === $score)>{{ $score }} Stars</option>
                        @endforeach
                    </select>
                    <textarea name="comment" rows="3" class="form-control" placeholder="Tell us what you like or what we should improve" required>{{ old('comment', $myPlatformReview?->comment) }}</textarea>
                    <button type="submit" class="w-full bg-dark text-white rounded-2xl py-3 font-bold hover:bg-slate-800 transition-all">
                        {{ $myPlatformReview ? 'Update Platform Review' : 'Submit Platform Review' }}
                    </button>
                </form>

                <div class="mt-6 space-y-3">
                    @forelse ($platformReviews as $review)
                        <div class="rounded-3xl bg-slate-50 border border-slate-100 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-sm font-bold text-dark">{{ $review->user->name }}</p>
                                <span class="text-sm font-black text-amber-500">
                                    <i class="ph-fill ph-star"></i> {{ $review->rating }}/5
                                </span>
                            </div>
                            <p class="text-sm text-slate-600 mt-2">{{ $review->comment }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No platform reviews yet.</p>
                    @endforelse
                </div>
            </div>
        </section>
        @endif
    </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const dashboardLiveUrl = "{{ route('dashboard.live') }}";
        const patientAppointmentsList = document.getElementById('patient-appointments-list');

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function formatPatientEta(totalSeconds) {
            const safeSeconds = Math.max(0, Number(totalSeconds || 0));
            const minutes = Math.floor(safeSeconds / 60);
            const seconds = String(safeSeconds % 60).padStart(2, '0');

            return `${minutes}m ${seconds}s`;
        }

        function tickPatientEtaTimers() {
            document.querySelectorAll('[data-patient-eta-seconds]').forEach((node) => {
                const nextSeconds = Math.max(0, Number(node.dataset.patientEtaSeconds || 0) - 1);
                node.dataset.patientEtaSeconds = String(nextSeconds);
                node.innerText = formatPatientEta(nextSeconds);
            });
        }

        async function refreshPatientAppointments() {
            const response = await fetch(dashboardLiveUrl, {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();
            if (!data.appointments || !patientAppointmentsList) {
                return;
            }

            if (!data.appointments.length) {
                patientAppointmentsList.innerHTML = '<p class="text-slate-500">No appointments yet. Visit Clinics and book your first token.</p>';
                return;
            }

            patientAppointmentsList.innerHTML = data.appointments.map((appointment) => {
                const queuePosition = appointment.queue_position ?? 0;
                const queueLabel = queuePosition > 0 ? queuePosition : (appointment.appointment_status === 'completed' ? 'Done' : 'Ready');
                const servingToken = appointment.currently_serving_token ? `#${appointment.currently_serving_token}` : '--';
                const etaSeconds = Number(appointment.eta_seconds || 0);
                const progressPercent = Math.max(5, Number(appointment.progress_percent || 0));
                const normalizedStatus = String(appointment.appointment_status || '').replaceAll('_', ' ');
                const prescriptionButtons = appointment.prescription_pdf_url
                    ? `
                        <a href="${escapeHtml(appointment.prescription_pdf_url)}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-primary/10 text-primary text-sm font-bold hover:bg-primary hover:text-white transition-all">
                            <i class="ph-bold ph-file-pdf"></i>
                            PDF
                        </a>
                        <a href="${escapeHtml(appointment.prescription_image_url)}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-secondary/10 text-secondary text-sm font-bold hover:bg-secondary hover:text-white transition-all">
                            <i class="ph-bold ph-image-square"></i>
                            Image
                        </a>
                    `
                    : '';

                return `
                <div class="premium-card rounded-[2rem] border border-slate-100 p-5 bg-gradient-to-br from-white to-slate-50">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center text-2xl shrink-0">
                                <i class="ph-fill ph-ticket"></i>
                            </div>
                            <div>
                                <div class="text-lg font-bold text-dark">Dr. ${escapeHtml(appointment.doctor_name)}</div>
                                <div class="text-slate-500 text-sm">${escapeHtml(appointment.specialization)} at ${escapeHtml(appointment.clinic_name)}</div>
                                <div class="text-xs text-slate-400 mt-1">${escapeHtml(appointment.date)} | ${escapeHtml(appointment.consultation_type)}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs uppercase tracking-wide text-slate-400">Token</div>
                            <div class="text-4xl font-black text-gradient">#${escapeHtml(appointment.token_number ?? '-')}</div>
                            <div class="text-xs font-bold uppercase text-accent">${escapeHtml(normalizedStatus)}</div>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-4 gap-3 mt-5">
                        <div class="rounded-3xl bg-blue-50 p-4">
                            <p class="text-[11px] uppercase tracking-wider text-slate-500 font-bold">Queue Position</p>
                            <p class="text-2xl font-black text-primary mt-1">${escapeHtml(queueLabel)}</p>
                        </div>
                        <div class="rounded-3xl bg-purple-50 p-4">
                            <p class="text-[11px] uppercase tracking-wider text-slate-500 font-bold">Now Serving</p>
                            <p class="text-2xl font-black text-secondary mt-1">${escapeHtml(servingToken)}</p>
                        </div>
                        <div class="rounded-3xl bg-green-50 p-4">
                            <p class="text-[11px] uppercase tracking-wider text-slate-500 font-bold">ETA Timer</p>
                            <p class="text-2xl font-black text-accent mt-1" data-patient-eta-seconds="${etaSeconds}">${escapeHtml(formatPatientEta(etaSeconds))}</p>
                        </div>
                        <div class="rounded-3xl bg-slate-50 p-4">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-bold">Progress</p>
                                <span class="text-xs font-black text-primary">${progressPercent}%</span>
                            </div>
                            <div class="w-full h-2.5 bg-white rounded-full overflow-hidden mt-3">
                                <div class="h-full bg-gradient-premium rounded-full transition-all duration-500" style="width: ${progressPercent}%;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3 mt-5">
                        <a href="${escapeHtml(appointment.details_url)}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-dark text-white text-sm font-bold hover:bg-slate-800 transition-all">
                            Open Queue Tracker
                            <i class="ph-bold ph-arrow-right"></i>
                        </a>
                        ${prescriptionButtons}
                    </div>
                </div>
            `;
            }).join('');
        }

        document.getElementById('symptom-form')?.addEventListener('submit', async (event) => {
            event.preventDefault();
            const symptoms = event.target.symptoms.value;
            const response = await fetch("{{ route('ai.symptom-check') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ symptoms })
            });
            const data = await response.json();
            document.getElementById('symptom-result').innerHTML = `<strong class="text-primary">${data.suggested_specialization}</strong><div class="mt-2">${data.message}</div>`;
        });

        document.getElementById('chatbot-form')?.addEventListener('submit', async (event) => {
            event.preventDefault();
            const message = event.target.message.value;
            const response = await fetch("{{ route('ai.chatbot') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message })
            });
            const data = await response.json();
            document.getElementById('chatbot-reply').innerText = data.reply;
        });

        if (patientAppointmentsList) {
            refreshPatientAppointments();
            setInterval(tickPatientEtaTimers, 1000);
            setInterval(refreshPatientAppointments, 5000);
        }
    </script>
@endsection
