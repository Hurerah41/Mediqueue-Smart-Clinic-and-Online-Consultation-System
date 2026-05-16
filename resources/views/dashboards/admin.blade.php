@extends('layouts.app', ['title' => 'Admin Dashboard | MediQueue'])

@php
    $weekdayLabels = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    $activeAdminSection = $activeAdminSection ?? 'reports';
@endphp

@section('content')
    <div class="dashboard-page grid lg:grid-cols-[280px_minmax(0,1fr)] gap-6 items-start">
        @include('dashboards.partials.sidebar')

        <div>
            <div class="flex flex-wrap items-start justify-between gap-6 mb-10">
                <div>
                    <p class="text-sm text-slate-500 font-bold">Clinic operations overview for today.</p>
                    <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-dark mt-2">{{ $clinic?->name ?? 'Clinic' }}</h1>
                    <p class="text-slate-500 mt-2">{{ $clinic?->area?->name }} | {{ $clinic?->address }}</p>
                </div>
    </div>

    @if ($activeAdminSection === 'reports')
    <div id="admin-analytics" class="grid md:grid-cols-5 gap-4 mb-8">
        @foreach ($stats as $label => $value)
            <div class="dashboard-card rounded-3xl p-6">
                <div class="text-xs uppercase tracking-widest text-slate-500 font-bold">{{ str_replace('_', ' ', $label) }}</div>
                <div class="text-4xl font-black text-gradient mt-3" data-live-stat="{{ $label }}">{{ $value }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid lg:grid-cols-2 gap-6 mb-8">
        <section class="dashboard-card rounded-[2rem] p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-extrabold text-dark">7-Day Appointments</h2>
                <i class="ph-fill ph-chart-line-up text-2xl text-primary"></i>
            </div>
            <canvas id="appointmentTrendChart" height="140"></canvas>
        </section>

        <section class="dashboard-card rounded-[2rem] p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-extrabold text-dark">7-Day Prescriptions</h2>
                <i class="ph-fill ph-pill text-2xl text-secondary"></i>
            </div>
            <canvas id="prescriptionTrendChart" height="140"></canvas>
        </section>
    </div>
    @endif

    @if ($activeAdminSection === 'doctors')
    <div class="grid xl:grid-cols-3 gap-6">
        <section id="admin-doctors" class="xl:col-span-2 dashboard-card rounded-[2rem] p-8">
            <h2 class="text-2xl font-extrabold mb-6 text-dark">Doctors & Weekly Schedules</h2>
            <div class="space-y-6">
                @foreach ($doctors as $doctor)
                    <div class="rounded-[2rem] border border-slate-100 p-6 hover:border-primary/30 transition-all">
                        <div class="flex flex-wrap justify-between gap-4 mb-5">
                            <div class="flex gap-4">
                                <div class="w-14 h-14 rounded-2xl bg-purple-50 text-secondary flex items-center justify-center text-2xl">
                                    <i class="ph-fill ph-stethoscope"></i>
                                </div>
                                <div>
                                    <div class="text-lg font-bold text-dark">Dr. {{ $doctor->user->name }}</div>
                                    <div class="text-slate-500 text-sm">{{ $doctor->specialization->name }} | License {{ $doctor->license_no }}</div>
                                    <div class="text-xs text-slate-400 mt-1">PKR {{ $doctor->consultation_fee }} | {{ $doctor->avg_consultation_minutes }} min avg</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $doctor->offers_online_consultation ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $doctor->offers_online_consultation ? 'Online + Physical' : 'Physical only' }}
                                </span>
                            </div>
                        </div>

                        <div class="mb-5 rounded-3xl bg-slate-50 border border-slate-100 p-4">
                            <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                                <div>
                                    <div class="text-sm font-extrabold text-dark">Assigned helpers / compounders</div>
                                    <div class="text-xs text-slate-500 mt-1">Helpers assigned here can manage this doctor's queue and prescription pickup list.</div>
                                </div>
                                @if ($doctor->helpers->isNotEmpty())
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($doctor->helpers as $helper)
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white border border-slate-200 px-3 py-1 text-xs font-bold text-slate-600">
                                                <i class="ph-fill ph-first-aid-kit text-primary"></i>
                                                {{ $helper->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="rounded-full bg-white border border-slate-200 px-3 py-1 text-xs font-bold text-slate-400">No helper assigned</span>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('admin.doctors.helper.update', $doctor) }}" class="grid sm:grid-cols-[1fr_auto_auto] gap-3">
                                @csrf
                                @method('PATCH')
                                <select name="helper_id" class="form-control">
                                    <option value="">Select helper to assign</option>
                                    @foreach (($helpers ?? collect()) as $helper)
                                        <option value="{{ $helper->id }}">
                                            {{ $helper->name }}{{ $helper->doctor_id && $helper->doctor_id !== $doctor->id ? ' - assigned to another doctor' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <button class="bg-dark text-white px-5 py-3 rounded-2xl font-bold hover:bg-primary transition-colors" type="submit">
                                    Assign Helper
                                </button>
                                <button class="bg-white border border-slate-200 text-slate-600 px-5 py-3 rounded-2xl font-bold hover:border-rose-200 hover:text-rose-600 transition-colors" type="submit" name="helper_id" value="">
                                    Clear
                                </button>
                            </form>
                        </div>

                        <div class="grid md:grid-cols-2 gap-3 mb-5">
                            @forelse ($doctor->schedules as $schedule)
                                <div class="rounded-3xl bg-slate-50 p-4 flex items-center justify-between gap-3">
                                    <div>
                                        <div class="font-bold text-dark">{{ $weekdayLabels[$schedule->weekday] ?? 'Day '.$schedule->weekday }}</div>
                                        <div class="text-sm text-slate-500">{{ $schedule->starts_at?->format('H:i') }} - {{ $schedule->ends_at?->format('H:i') }} | {{ $schedule->slot_limit }} slots</div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold {{ $schedule->is_active ? 'text-accent' : 'text-slate-400' }}">{{ $schedule->is_active ? 'Active' : 'Off' }}</span>
                                        <form method="POST" action="{{ route('admin.doctors.schedules.destroy', [$doctor, $schedule]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-9 h-9 rounded-full bg-white text-rose-500 flex items-center justify-center hover:bg-rose-50" aria-label="Delete schedule">
                                                <i class="ph-bold ph-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500">No schedule configured yet.</p>
                            @endforelse
                        </div>

                        <form method="POST" action="{{ route('admin.doctors.schedules.store', $doctor) }}" class="grid md:grid-cols-5 gap-3 border-t border-slate-100 pt-5">
                            @csrf
                            <select name="weekday" class="form-control" required>
                                @foreach ($weekdayLabels as $index => $label)
                                    <option value="{{ $index }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <input type="time" name="starts_at" class="form-control" required>
                            <input type="time" name="ends_at" class="form-control" required>
                            <input type="number" name="slot_limit" min="1" value="30" class="form-control" required>
                            <select name="is_active" class="form-control" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            <button class="md:col-span-5 bg-dark text-white py-3 rounded-2xl font-bold hover:shadow-lg transition-all" type="submit">Save Schedule</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="dashboard-card rounded-[2rem] p-8">
            <h2 class="text-xl font-extrabold mb-5 text-dark">Add Doctor</h2>
            <form method="POST" action="{{ route('admin.doctors.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input name="name" class="form-control" placeholder="Doctor name" required>
                <input name="email" type="email" class="form-control" placeholder="Doctor email" required>
                <input name="phone" class="form-control" placeholder="Phone">
                <select name="specialization_id" class="form-control" required>
                    <option value="">Select specialization</option>
                    @foreach ($specializations as $specialization)
                        <option value="{{ $specialization->id }}">{{ $specialization->name }}</option>
                    @endforeach
                </select>
                <input name="license_no" class="form-control" placeholder="License number" required>
                <input name="experience_years" type="number" min="1" max="60" value="5" class="form-control" placeholder="Experience years" required>
                <input name="consultation_fee" type="number" min="0" class="form-control" placeholder="Fee" required>
                <input name="avg_consultation_minutes" type="number" min="5" value="15" class="form-control" placeholder="Average consultation minutes" required>
                <input name="profile_photo" type="file" accept="image/png,image/jpeg,image/webp" class="form-control">
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="offers_online_consultation" value="1" class="rounded border-slate-300 text-primary" checked>
                    Offers online consultation
                </label>
                <textarea name="bio" rows="3" class="form-control" placeholder="Doctor bio"></textarea>
                <button class="w-full bg-gradient-premium text-white py-3 rounded-2xl font-bold shadow-glow" type="submit">Create Doctor</button>
            </form>
            <p class="text-xs text-slate-500 mt-4">Temporary password assigned: Doctor@12345</p>
        </section>
    </div>
    @endif

    @if ($activeAdminSection === 'settings')
    <div class="grid gap-6">
        <section class="dashboard-card rounded-[2rem] p-8">
            <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-extrabold text-dark">Clinic Profile & Branding</h2>
                    <p class="text-sm text-slate-500 mt-1">Update your clinic public profile, logo, and brand colors.</p>
                </div>
                @if ($clinic?->logo_path)
                    <img src="{{ asset('storage/'.$clinic->logo_path) }}" alt="{{ $clinic->name }} Logo" class="w-16 h-16 rounded-2xl object-cover border border-slate-100">
                @endif
            </div>

            <form method="POST" action="{{ route('admin.clinic-profile.update') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input name="name" class="form-control" placeholder="Clinic name" value="{{ old('name', $clinic?->name) }}" required>
                <input name="phone" class="form-control" placeholder="Clinic phone" value="{{ old('phone', $clinic?->phone) }}">
                <input name="address" class="form-control" placeholder="Clinic address" value="{{ old('address', $clinic?->address) }}" required>
                <input name="brand_tagline" class="form-control" placeholder="Brand tagline" value="{{ old('brand_tagline', $clinic?->brand_tagline) }}">
                <div class="grid grid-cols-2 gap-3">
                    <input type="time" name="opens_at" class="form-control" value="{{ old('opens_at', $clinic?->opens_at?->format('H:i')) }}">
                    <input type="time" name="closes_at" class="form-control" value="{{ old('closes_at', $clinic?->closes_at?->format('H:i')) }}">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <input type="color" name="brand_primary_color" class="form-control h-14 p-2" value="{{ old('brand_primary_color', $clinic?->brand_primary_color ?? '#2563EB') }}" required>
                    <input type="color" name="brand_secondary_color" class="form-control h-14 p-2" value="{{ old('brand_secondary_color', $clinic?->brand_secondary_color ?? '#7C3AED') }}" required>
                </div>
                <input type="file" name="logo" accept="image/png,image/jpeg,image/webp" class="form-control">
                <button class="w-full bg-gradient-premium text-white py-3 rounded-2xl font-bold shadow-glow" type="submit">Save Clinic Profile</button>
            </form>
        </section>
    </div>
    @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chartLabels = @json($trendLabels);
        const adminDashboardLiveUrl = "{{ route('dashboard.live') }}";

        const appointmentTrendChart = document.getElementById('appointmentTrendChart');
        const prescriptionTrendChart = document.getElementById('prescriptionTrendChart');

        if (appointmentTrendChart) {
            new Chart(appointmentTrendChart, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Appointments',
                        data: @json($appointmentTrend),
                        borderColor: '#2563EB',
                        backgroundColor: 'rgba(37,99,235,0.12)',
                        fill: true,
                        tension: 0.45
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        if (prescriptionTrendChart) {
            new Chart(prescriptionTrendChart, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Prescriptions',
                        data: @json($prescriptionTrend),
                        backgroundColor: 'rgba(124,58,237,0.75)',
                        borderRadius: 14
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        async function refreshAdminStats() {
            const response = await fetch(adminDashboardLiveUrl, {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) return;

            const data = await response.json();
            Object.entries(data.stats || {}).forEach(([key, value]) => {
                const node = document.querySelector(`[data-live-stat="${key}"]`);
                if (node) node.innerText = value;
            });
        }

        refreshAdminStats();
        setInterval(refreshAdminStats, 5000);
    </script>
@endsection
