@extends('layouts.app', ['title' => 'Helper Dashboard | MediQueue'])

@section('content')
    <div class="dashboard-page grid lg:grid-cols-[280px_minmax(0,1fr)] gap-6 items-start">
        @include('dashboards.partials.sidebar')

        <div>
            <div class="flex flex-wrap items-start justify-between gap-6 mb-8">
                <div>
                    <p class="text-sm text-slate-500 font-bold">Clinic queue operations</p>
                    <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-dark mt-2">Helper Desk</h1>
                    <p class="text-slate-500 mt-2">
                        {{ $helper->clinic?->name }}
                        @if ($helper->assignedDoctor)
                            | Supporting Dr. {{ $helper->assignedDoctor->user->name }}
                        @endif
                    </p>
                </div>

                <form method="POST" action="{{ route('helper.queue.next') }}">
                    @csrf
                    <button type="submit" class="dashboard-action px-6 py-3 rounded-2xl font-extrabold transition-all inline-flex items-center gap-2">
                        <i class="ph-bold ph-arrow-bend-up-right"></i>
                        Call Next Patient
                    </button>
                </form>
            </div>

            <div class="grid md:grid-cols-3 gap-4 mb-8">
                <div class="dashboard-card rounded-3xl p-6">
                    <div class="text-xs uppercase tracking-widest text-slate-500 font-bold">Waiting</div>
                    <div class="text-4xl font-black text-gradient mt-3">{{ $stats['waiting'] }}</div>
                </div>
                <div class="dashboard-card rounded-3xl p-6">
                    <div class="text-xs uppercase tracking-widest text-slate-500 font-bold">Serving</div>
                    <div class="text-4xl font-black text-gradient mt-3">{{ $stats['serving'] }}</div>
                </div>
                <div class="dashboard-card rounded-3xl p-6">
                    <div class="text-xs uppercase tracking-widest text-slate-500 font-bold">Completed</div>
                    <div class="text-4xl font-black text-gradient mt-3">{{ $stats['completed'] }}</div>
                </div>
            </div>

            <div class="grid xl:grid-cols-[1.25fr_0.75fr] gap-6">
                <section class="dashboard-card rounded-[2rem] p-6 sm:p-8">
                    <div class="flex items-center justify-between gap-4 mb-6">
                        <div>
                            <h2 class="text-2xl font-extrabold text-dark">Today's Queue</h2>
                            <p class="text-sm text-slate-500 mt-1">Monitor tokens, call the next patient, and mark consultations complete.</p>
                        </div>
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-50 text-emerald-600 text-xs font-bold uppercase tracking-wider">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                            Active Queue
                        </span>
                    </div>

                    <div class="space-y-4">
                        @forelse ($appointments as $appointment)
                            @php
                                $token = $appointment->queueToken;
                                $statusLabel = match ($token?->status) {
                                    \App\Models\QueueToken::STATUS_WAITING => 'Waiting',
                                    \App\Models\QueueToken::STATUS_CALLED => 'Serving',
                                    \App\Models\QueueToken::STATUS_COMPLETED => 'Completed',
                                    default => ucfirst(str_replace('_', ' ', $appointment->status)),
                                };
                                $statusClasses = match ($token?->status) {
                                    \App\Models\QueueToken::STATUS_WAITING => 'bg-amber-50 text-amber-700',
                                    \App\Models\QueueToken::STATUS_CALLED => 'bg-blue-50 text-primary',
                                    \App\Models\QueueToken::STATUS_COMPLETED => 'bg-emerald-50 text-emerald-700',
                                    default => 'bg-slate-100 text-slate-600',
                                };
                            @endphp
                            <div class="dashboard-card-soft rounded-[1.75rem] p-5">
                                <div class="flex flex-wrap items-start justify-between gap-4">
                                    <div class="flex items-start gap-4">
                                        <div class="w-14 h-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center text-2xl shrink-0">
                                            <i class="ph-fill ph-ticket"></i>
                                        </div>
                                        <div>
                                            <div class="text-xs uppercase tracking-[0.18em] text-slate-400 font-black">Token Number</div>
                                            <div class="text-3xl font-black text-dark mt-1">#{{ $token?->token_number ?? '-' }}</div>
                                            <div class="text-lg font-bold text-dark mt-2">{{ $appointment->patient->name }}</div>
                                            <div class="text-sm text-slate-500">{{ $appointment->doctor->user->name }} | {{ $appointment->doctor->specialization->name }}</div>
                                            <div class="text-xs text-slate-400 mt-2">Type: {{ ucfirst($appointment->consultation_type) }} | Time: {{ $appointment->appointment_time ?? 'Walk-in' }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold uppercase {{ $statusClasses }}">{{ $statusLabel }}</span>
                                        <div class="mt-3 text-xs text-slate-500">Patient ID: {{ $appointment->patient->id }}</div>
                                        <div class="text-xs text-slate-500">{{ $appointment->patient->phone ?? 'No phone' }}</div>
                                    </div>
                                </div>

                                <div class="mt-5 flex flex-wrap gap-3">
                                    @if (($token?->status) === \App\Models\QueueToken::STATUS_CALLED)
                                        <form method="POST" action="{{ route('helper.appointments.complete', $appointment) }}">
                                            @csrf
                                            <button type="submit" class="bg-dark text-white px-5 py-3 rounded-2xl text-sm font-bold hover:bg-primary transition-colors">
                                                Mark Completed
                                            </button>
                                        </form>
                                    @endif

                                    @if ($appointment->prescription)
                                        <a href="{{ route('prescriptions.download', $appointment->prescription) }}" class="px-5 py-3 rounded-2xl border border-slate-200 text-sm font-bold text-slate-600 hover:border-primary hover:text-primary transition-colors">
                                            Download Prescription PDF
                                        </a>
                                        <a href="{{ route('prescriptions.image', $appointment->prescription) }}" class="px-5 py-3 rounded-2xl border border-slate-200 text-sm font-bold text-slate-600 hover:border-primary hover:text-primary transition-colors">
                                            Download Prescription Image
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="rounded-[1.75rem] border border-slate-100 bg-slate-50/70 p-8 text-center">
                                <h3 class="text-lg font-extrabold text-dark">No appointments in today's queue</h3>
                                <p class="text-sm text-slate-500 mt-2">New patient tokens will appear here automatically.</p>
                            </div>
                        @endforelse
                    </div>
                </section>

                <section class="dashboard-card rounded-[2rem] p-6 sm:p-8">
                    <h2 class="text-2xl font-extrabold text-dark">Prescription Pickup</h2>
                    <p class="text-sm text-slate-500 mt-1 mb-6">Prescriptions written by doctors appear here so the helper can assist patients with medicines.</p>

                    <div class="space-y-4">
                        @forelse ($prescriptions as $prescription)
                            <div class="dashboard-card-soft rounded-[1.5rem] p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="text-sm font-bold text-dark">{{ $prescription->patient->name }}</div>
                                        <div class="text-xs text-slate-500 mt-1">Dr. {{ $prescription->doctor->user->name }}</div>
                                        <div class="text-xs text-slate-400 mt-1">Token #{{ $prescription->appointment?->queueToken?->token_number ?? '-' }}</div>
                                    </div>
                                    <span class="text-xs font-bold uppercase text-primary">{{ optional($prescription->issued_at)->format('h:i A') }}</span>
                                </div>

                                <div class="mt-4 space-y-2">
                                    @foreach ($prescription->items->take(3) as $item)
                                        <div class="rounded-2xl bg-white px-3 py-2 border border-slate-100">
                                            <div class="text-sm font-bold text-dark">{{ $item->medicine_name }}</div>
                                            <div class="text-xs text-slate-500">{{ $item->dosage }} | {{ $item->frequency }} | {{ $item->duration }}</div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-4 flex flex-wrap gap-2">
                                    <a href="{{ route('prescriptions.download', $prescription) }}" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:border-primary hover:text-primary transition-colors">
                                        PDF
                                    </a>
                                    <a href="{{ route('prescriptions.image', $prescription) }}" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:border-primary hover:text-primary transition-colors">
                                        Image
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-[1.5rem] border border-slate-100 bg-slate-50/70 p-6 text-center">
                                <p class="text-sm text-slate-500">No prescriptions issued today yet.</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
