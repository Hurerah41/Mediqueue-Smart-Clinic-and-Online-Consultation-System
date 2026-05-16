@extends('layouts.app', ['title' => 'Owner Dashboard | MediQueue'])

@section('content')
    @php
        $activeOwnerSection = $activeOwnerSection ?? 'overview';
    @endphp

    <div class="dashboard-page grid lg:grid-cols-[280px_minmax(0,1fr)] gap-6 items-start">
        @include('dashboards.partials.sidebar')

        <div>
            <div class="flex flex-wrap items-start justify-between gap-6 mb-10" id="owner-overview">
                <div>
                    <p class="text-sm text-slate-500 font-bold">Manage the full MediQueue platform.</p>
                    <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-dark mt-2">Platform Super Admin</h1>
                    <p class="text-slate-500 mt-2">Manage all clinics, clinic admins, doctors, and patients across MediQueue.</p>
                </div>
            </div>

    @if ($activeOwnerSection === 'overview')
    @php
        $ownerMetrics = $ownerMetrics ?? [];
        $ownerCharts = $ownerCharts ?? ['labels' => [], 'appointments' => [], 'users' => [], 'roles' => ['labels' => [], 'values' => []]];
        $overviewCards = [
            ['label' => 'Total Clinics', 'key' => 'clinics', 'value' => $stats['clinics'] ?? 0, 'icon' => 'ph-hospital', 'tone' => 'bg-blue-50 text-primary', 'hint' => ($ownerMetrics['active_clinics'] ?? 0).' active'],
            ['label' => 'Pending Apps', 'key' => 'pending_apps', 'value' => $stats['pending_apps'] ?? 0, 'icon' => 'ph-clipboard-text', 'tone' => 'bg-amber-50 text-amber-600', 'hint' => 'Needs owner review'],
            ['label' => 'Total Doctors', 'key' => 'doctors', 'value' => $stats['doctors'] ?? 0, 'icon' => 'ph-stethoscope', 'tone' => 'bg-purple-50 text-secondary', 'hint' => ($stats['helpers'] ?? 0).' helpers'],
            ['label' => 'Patients', 'key' => 'patients', 'value' => $stats['patients'] ?? 0, 'icon' => 'ph-users-three', 'tone' => 'bg-green-50 text-accent', 'hint' => ($ownerMetrics['verified_users'] ?? 0).' verified users'],
            ['label' => 'Clinic Admins', 'key' => 'clinic_admins', 'value' => $stats['clinic_admins'] ?? 0, 'icon' => 'ph-identification-card', 'tone' => 'bg-slate-100 text-dark', 'hint' => 'Operational accounts'],
            ['label' => 'Helpers', 'key' => 'helpers', 'value' => $stats['helpers'] ?? 0, 'icon' => 'ph-first-aid-kit', 'tone' => 'bg-rose-50 text-rose-600', 'hint' => ($ownerMetrics['unassigned_helpers'] ?? 0).' unassigned'],
        ];
    @endphp

    <div class="grid sm:grid-cols-2 xl:grid-cols-6 gap-4 mb-8">
        @foreach ($overviewCards as $card)
            <div class="dashboard-card rounded-[1.6rem] p-5 min-h-[148px] flex flex-col justify-between">
                <div class="flex items-start justify-between gap-3">
                    <div class="w-11 h-11 rounded-2xl {{ $card['tone'] }} flex items-center justify-center text-xl">
                        <i class="ph-fill {{ $card['icon'] }}"></i>
                    </div>
                    <span class="text-[10px] uppercase tracking-[0.16em] font-black text-slate-400">Live</span>
                </div>
                <div>
                    <div class="text-3xl font-black text-dark" data-owner-live-stat="{{ $card['key'] }}">{{ $card['value'] }}</div>
                    <div class="mt-1 text-xs uppercase tracking-[0.16em] text-slate-400 font-black">{{ $card['label'] }}</div>
                    <div class="mt-2 text-xs font-bold text-slate-500">{{ $card['hint'] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid xl:grid-cols-[1.4fr_0.8fr] gap-6 mb-8">
        <section class="dashboard-card rounded-[2rem] p-6">
            <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] font-black text-primary">Platform Growth</p>
                    <h2 class="mt-2 text-2xl font-black text-dark">Appointments and new users</h2>
                </div>
                <div class="flex gap-2 text-xs font-bold">
                    <span class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1 text-primary"><span class="w-2 h-2 rounded-full bg-primary"></span>Visits</span>
                    <span class="inline-flex items-center gap-2 rounded-full bg-purple-50 px-3 py-1 text-secondary"><span class="w-2 h-2 rounded-full bg-secondary"></span>Users</span>
                </div>
            </div>
            <canvas id="ownerGrowthChart" height="120"></canvas>
        </section>

        <section class="dashboard-card rounded-[2rem] p-6">
            <div class="flex items-start justify-between gap-4 mb-5">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] font-black text-primary">User Mix</p>
                    <h2 class="mt-2 text-2xl font-black text-dark">Roles breakdown</h2>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-slate-50 text-dark flex items-center justify-center text-xl">
                    <i class="ph-fill ph-chart-donut"></i>
                </div>
            </div>
            <canvas id="ownerRoleChart" height="150"></canvas>
        </section>
    </div>

    <div class="grid lg:grid-cols-3 gap-6 mb-8">
        <section class="dashboard-card rounded-[2rem] p-6">
            <p class="text-xs uppercase tracking-[0.2em] font-black text-slate-400">Clinic Status</p>
            <div class="mt-5 grid grid-cols-2 gap-3">
                <div class="rounded-3xl bg-green-50 p-5">
                    <div class="text-3xl font-black text-green-700">{{ $ownerMetrics['active_clinics'] ?? 0 }}</div>
                    <div class="text-xs font-black uppercase tracking-[0.16em] text-green-600 mt-2">Active</div>
                </div>
                <div class="rounded-3xl bg-rose-50 p-5">
                    <div class="text-3xl font-black text-rose-700">{{ $ownerMetrics['inactive_clinics'] ?? 0 }}</div>
                    <div class="text-xs font-black uppercase tracking-[0.16em] text-rose-600 mt-2">Inactive</div>
                </div>
            </div>
        </section>

        <section class="dashboard-card rounded-[2rem] p-6">
            <p class="text-xs uppercase tracking-[0.2em] font-black text-slate-400">Queue Health</p>
            <div class="mt-5 space-y-3">
                @foreach ([['Waiting', $ownerMetrics['waiting_tokens'] ?? 0, 'bg-amber-500'], ['Serving', $ownerMetrics['serving_tokens'] ?? 0, 'bg-primary'], ['Completed', $ownerMetrics['completed_tokens'] ?? 0, 'bg-accent']] as [$label, $value, $bar])
                    <div>
                        <div class="flex justify-between text-sm font-black text-dark"><span>{{ $label }}</span><span>{{ $value }}</span></div>
                        <div class="mt-2 h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full {{ $bar }}" style="width: {{ min(100, max(8, (int) $value * 12)) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="dashboard-card rounded-[2rem] p-6">
            <p class="text-xs uppercase tracking-[0.2em] font-black text-slate-400">User Quality</p>
            <div class="mt-5 space-y-4">
                <div class="flex items-center justify-between rounded-3xl bg-slate-50 p-4">
                    <span class="text-sm font-black text-dark">Verified users</span>
                    <span class="text-xl font-black text-accent">{{ $ownerMetrics['verified_users'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between rounded-3xl bg-slate-50 p-4">
                    <span class="text-sm font-black text-dark">Unverified users</span>
                    <span class="text-xl font-black text-amber-600">{{ $ownerMetrics['unverified_users'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between rounded-3xl bg-slate-50 p-4">
                    <span class="text-sm font-black text-dark">Unassigned helpers</span>
                    <span class="text-xl font-black text-secondary">{{ $ownerMetrics['unassigned_helpers'] ?? 0 }}</span>
                </div>
            </div>
        </section>
    </div>
    @endif

    @if ($activeOwnerSection === 'clinics')
    <div class="grid xl:grid-cols-3 gap-6 mb-8">
        <section id="owner-clinics" class="xl:col-span-2 dashboard-card rounded-[2rem] p-8">
            <h2 class="text-2xl font-extrabold mb-6 text-dark">All Clinics</h2>
            <div class="space-y-4" id="owner-clinic-list">
                @foreach ($clinics as $clinic)
                    <div class="rounded-[2rem] border border-slate-100 p-5">
                        <div class="flex flex-wrap justify-between gap-4">
                            <div>
                                <div class="text-lg font-bold text-dark">{{ $clinic->name }}</div>
                                <div class="text-sm text-slate-500">{{ $clinic->area->name }} | {{ $clinic->address }}</div>
                                <div class="text-xs text-slate-400 mt-2">Admins: {{ $clinic->users->where('role', 'admin')->count() }} | Doctors: {{ $clinic->users->where('role', 'doctor')->count() }}</div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex mt-2 px-3 py-1 rounded-full text-xs font-bold {{ $clinic->is_active ? 'bg-green-100 text-green-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $clinic->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="dashboard-card rounded-[2rem] p-8">
            <h2 class="text-xl font-extrabold mb-5 text-dark">Create Clinic + Admin</h2>
            <form method="POST" action="{{ route('owner.clinics.store') }}" class="space-y-4">
                @csrf
                <div class="rounded-[1.5rem] border border-slate-100 bg-slate-50/70 p-4 space-y-3">
                    <label class="block text-xs font-black uppercase tracking-[0.18em] text-slate-400">Area</label>
                    <select name="area_id" class="form-control">
                        <option value="">Select existing area</option>
                        @foreach ($areas as $area)
                            <option value="{{ $area->id }}" @selected(old('area_id') == $area->id)>{{ $area->name }}{{ $area->city ? ' - '.$area->city : '' }}</option>
                        @endforeach
                    </select>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <input name="new_area_name" value="{{ old('new_area_name') }}" class="form-control" placeholder="Or add new area name">
                        <input name="new_area_city" value="{{ old('new_area_city', 'Karachi') }}" class="form-control" placeholder="City">
                    </div>
                    <p class="text-xs text-slate-500">Choose an existing area or type a new one. New areas are saved automatically with the clinic.</p>
                </div>
                <input name="clinic_name" class="form-control" placeholder="Clinic name" required>
                <input name="clinic_phone" class="form-control" placeholder="Clinic phone">
                <input name="address" class="form-control" placeholder="Clinic address" required>
                <div class="grid grid-cols-2 gap-3">
                    <input type="time" name="opens_at" class="form-control">
                    <input type="time" name="closes_at" class="form-control">
                </div>
                <input name="admin_name" class="form-control" placeholder="Clinic admin name" required>
                <input type="email" name="admin_email" class="form-control" placeholder="Clinic admin email" required>
                <input name="admin_phone" class="form-control" placeholder="Clinic admin phone">
                <button class="w-full bg-gradient-premium text-white py-3 rounded-2xl font-bold shadow-glow" type="submit">Create Clinic</button>
            </form>
            <p class="text-xs text-slate-500 mt-4">Temporary clinic admin password: Admin@12345</p>
        </section>
    </div>
    @endif

    @if ($activeOwnerSection === 'applications')
    <section id="owner-applications" class="dashboard-card rounded-[2rem] p-8 mb-8">
        <h2 class="text-2xl font-extrabold mb-6 text-dark">Pending Clinic Applications</h2>
        <div class="space-y-4" id="owner-application-list">
            @forelse ($clinicApplications as $application)
                <div class="rounded-[2rem] border border-slate-100 p-5">
                    <div class="flex flex-wrap justify-between gap-4">
                        <div>
                            <div class="text-lg font-bold text-dark">{{ $application->clinic_name }}</div>
                            <div class="text-sm text-slate-500">{{ $application->area->name }} | {{ $application->address }}</div>
                            <div class="text-xs text-slate-400 mt-2">{{ $application->admin_name }} | {{ $application->admin_email }} | {{ $application->admin_phone }}</div>
                        </div>
                    </div>
                    <div class="grid lg:grid-cols-2 gap-3 mt-4">
                        <form method="POST" action="{{ route('owner.clinic-applications.approve', $application) }}" class="space-y-3">
                            @csrf
                            <input name="owner_notes" class="form-control" placeholder="Approval notes">
                            <button class="w-full bg-accent text-white py-3 rounded-2xl font-bold" type="submit">Approve Clinic</button>
                        </form>
                        <form method="POST" action="{{ route('owner.clinic-applications.reject', $application) }}" class="space-y-3">
                            @csrf
                            <textarea name="owner_notes" class="form-control" rows="4" placeholder="Reason for rejection" required></textarea>
                            <button class="w-full bg-rose-600 text-white py-3 rounded-2xl font-bold" type="submit">Reject Application</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-slate-500">No pending clinic applications right now.</p>
            @endforelse
        </div>
    </section>
    @endif

    @if ($activeOwnerSection === 'users')
    @php
        $userRoleStyles = [
            'super_admin' => 'bg-slate-900 text-white',
            'admin' => 'bg-blue-50 text-primary',
            'doctor' => 'bg-purple-50 text-secondary',
            'helper' => 'bg-green-50 text-accent',
            'patient' => 'bg-slate-100 text-slate-600',
        ];
    @endphp
    <section id="owner-users" class="space-y-6">
        <div class="dashboard-card rounded-[2rem] p-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] font-black text-primary">Access Control</p>
                    <h2 class="mt-2 text-3xl font-black text-dark">Users, roles, and assignments</h2>
                    <p class="mt-2 text-sm font-medium text-slate-500">Change user roles, attach clinic staff to clinics, and assign helpers to doctors.</p>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach (['admin' => 'Admins', 'doctor' => 'Doctors', 'helper' => 'Helpers', 'patient' => 'Patients'] as $role => $label)
                        <div class="rounded-2xl bg-slate-50 border border-slate-100 px-4 py-3 text-center">
                            <div class="text-xl font-black text-dark">{{ $users->where('role', $role)->count() }}</div>
                            <div class="text-[10px] uppercase tracking-[0.16em] font-black text-slate-400">{{ $label }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="space-y-4" id="owner-user-list">
            @foreach ($users as $member)
                <form method="POST" action="{{ route('owner.users.update', $member) }}" class="owner-user-form dashboard-card rounded-[2rem] p-5">
                    @csrf
                    @method('PATCH')
                    <div class="grid xl:grid-cols-[1.1fr_1.5fr_auto] gap-5 items-center">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary to-secondary text-white flex items-center justify-center text-xl font-black shrink-0">
                                {{ strtoupper(substr($member->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-lg font-black text-dark truncate">{{ $member->name }}</h3>
                                    <span class="rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-[0.14em] {{ $userRoleStyles[$member->role] ?? 'bg-slate-100 text-slate-600' }}">
                                        {{ $member->isSuperAdmin() ? 'Owner' : str_replace('_', ' ', $member->role) }}
                                    </span>
                                </div>
                                <p class="text-sm font-medium text-slate-500 truncate">{{ $member->email }}</p>
                                <div class="mt-2 flex flex-wrap gap-2 text-[11px] font-bold">
                                    <span class="rounded-full bg-slate-50 px-3 py-1 text-slate-500">{{ $member->phone ?: 'No phone' }}</span>
                                    <span class="rounded-full {{ $member->is_verified ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700' }} px-3 py-1">{{ $member->is_verified ? 'Verified' : 'Not verified' }}</span>
                                    <span class="rounded-full bg-slate-50 px-3 py-1 text-slate-500">Joined {{ $member->created_at?->format('d M Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-4 gap-3">
                            <input name="name" value="{{ $member->name }}" class="form-control" @disabled($member->isSuperAdmin())>
                            <input name="phone" value="{{ $member->phone }}" class="form-control" placeholder="Phone" @disabled($member->isSuperAdmin())>
                            <select name="role" class="form-control owner-role-select" @disabled($member->isSuperAdmin())>
                                @foreach (['admin' => 'Clinic Admin', 'doctor' => 'Doctor', 'helper' => 'Doctor Helper', 'patient' => 'Patient'] as $value => $label)
                                    <option value="{{ $value }}" @selected($member->role === $value)>{{ $member->isSuperAdmin() ? 'Owner' : $label }}</option>
                                @endforeach
                            </select>
                            <select name="clinic_id" class="form-control owner-clinic-select" @disabled($member->isSuperAdmin())>
                                <option value="">No clinic</option>
                                @foreach ($clinics as $clinic)
                                    <option value="{{ $clinic->id }}" @selected($member->clinic_id === $clinic->id)>{{ $clinic->name }}</option>
                                @endforeach
                            </select>
                            <select name="doctor_id" class="md:col-span-2 form-control owner-doctor-select" @disabled($member->isSuperAdmin())>
                                <option value="">No doctor assignment</option>
                                @foreach ($doctors as $doctor)
                                    <option value="{{ $doctor->id }}" data-clinic-id="{{ $doctor->clinic_id }}" @selected($member->doctor_id === $doctor->id)>{{ $doctor->user->name }}{{ $doctor->clinic ? ' - '.$doctor->clinic->name : '' }}</option>
                                @endforeach
                            </select>
                            <div class="md:col-span-2 rounded-2xl bg-slate-50 border border-slate-100 px-4 py-3 text-xs font-bold text-slate-500">
                                Clinic: {{ $member->clinic?->name ?? 'None' }}<br>
                                Doctor: {{ $member->assignedDoctor?->user?->name ?? 'None' }}
                            </div>
                        </div>

                        <div class="flex xl:flex-col gap-2 justify-end">
                            @if (! $member->isSuperAdmin())
                                <button class="bg-dark text-white px-5 py-3 rounded-2xl font-bold hover:bg-primary transition-colors" type="submit">Update</button>
                            @else
                                <span class="text-xs font-bold text-primary px-5 py-3">Protected owner</span>
                            @endif
                        </div>
                    </div>
                </form>
            @endforeach
        </div>
    </section>
    @endif

    @if ($activeOwnerSection === 'settings')
        <section class="dashboard-card rounded-[2rem] p-8">
            <h2 class="text-2xl font-extrabold mb-2 text-dark">Platform Settings</h2>
            <p class="text-slate-600 max-w-2xl mb-8">Use this page to control the platform identity, support email, clinic verification policy, commission percentage, and how early queue alerts should trigger.</p>

            <form method="POST" action="{{ route('owner.settings.update') }}" class="grid lg:grid-cols-2 gap-5">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-dark mb-2">Platform Name</label>
                    <input name="platform_name" class="form-control" value="{{ old('platform_name', $platformSettings->platform_name ?? 'MediQueue') }}" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-dark mb-2">Support Email</label>
                    <input name="support_email" type="email" class="form-control" value="{{ old('support_email', $platformSettings->support_email ?? 'support@mediqueue.test') }}" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-dark mb-2">Platform Commission %</label>
                    <input name="commission_percent" type="number" min="0" max="100" class="form-control" value="{{ old('commission_percent', $platformSettings->commission_percent ?? 10) }}" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-dark mb-2">Queue Near-Turn Alert Threshold</label>
                    <input name="queue_alert_threshold" type="number" min="1" max="20" class="form-control" value="{{ old('queue_alert_threshold', $platformSettings->queue_alert_threshold ?? 2) }}" required>
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-sm font-semibold text-dark mb-2">Clinic Verification Policy</label>
                    <textarea name="clinic_verification_policy" rows="4" class="form-control">{{ old('clinic_verification_policy', $platformSettings->clinic_verification_policy ?? '') }}</textarea>
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-sm font-semibold text-dark mb-2">Owner Internal Notes</label>
                    <textarea name="owner_notes" rows="3" class="form-control">{{ old('owner_notes', $platformSettings->owner_notes ?? '') }}</textarea>
                </div>
                <button type="submit" class="lg:col-span-2 bg-gradient-premium text-white py-4 rounded-2xl font-bold shadow-glow">Save Platform Settings</button>
            </form>

            <form method="POST" action="{{ route('owner.settings.test-email') }}" class="mt-5">
                @csrf
                <button type="submit" class="w-full border border-primary/20 text-primary py-4 rounded-2xl font-bold hover:bg-primary hover:text-white transition-all">
                    Send Test Email to {{ $platformSettings->support_email ?? 'Support Email' }}
                </button>
            </form>
        </section>
    @endif
        </div>
    </div>

    @if ($activeOwnerSection === 'overview')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endif

    <script>
        const ownerDashboardLiveUrl = "{{ route('dashboard.live') }}";
        const ownerUserList = document.getElementById('owner-user-list');
        const ownerClinicList = document.getElementById('owner-clinic-list');
        const ownerApplicationList = document.getElementById('owner-application-list');
        const ownerUserUpdateRouteTemplate = "{{ route('owner.users.update', 0) }}";
        const ownerCharts = @json($ownerCharts ?? null);
        let ownerUsersDirtyUntil = 0;

        if (ownerCharts && window.Chart) {
            const growthCanvas = document.getElementById('ownerGrowthChart');
            const roleCanvas = document.getElementById('ownerRoleChart');

            if (growthCanvas) {
                new Chart(growthCanvas, {
                    type: 'line',
                    data: {
                        labels: ownerCharts.labels || [],
                        datasets: [
                            {
                                label: 'Appointments',
                                data: ownerCharts.appointments || [],
                                borderColor: '#2563EB',
                                backgroundColor: 'rgba(37, 99, 235, 0.12)',
                                fill: true,
                                tension: 0.45,
                                borderWidth: 3,
                            },
                            {
                                label: 'New Users',
                                data: ownerCharts.users || [],
                                borderColor: '#7C3AED',
                                backgroundColor: 'rgba(124, 58, 237, 0.08)',
                                fill: true,
                                tension: 0.45,
                                borderWidth: 3,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: 'rgba(148, 163, 184, 0.16)' } },
                            x: { grid: { display: false } },
                        },
                    },
                });
            }

            if (roleCanvas) {
                new Chart(roleCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: ownerCharts.roles?.labels || [],
                        datasets: [{
                            data: ownerCharts.roles?.values || [],
                            backgroundColor: ['#2563EB', '#7C3AED', '#22C55E', '#94A3B8'],
                            borderColor: '#FFFFFF',
                            borderWidth: 5,
                            hoverOffset: 8,
                        }],
                    },
                    options: {
                        cutout: '68%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { usePointStyle: true, boxWidth: 8, font: { weight: '700' } },
                            },
                        },
                    },
                });
            }
        }

        function escapeOwnerHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function renderClinicApplications(applications) {
            if (!ownerApplicationList || !applications) return;

            if (!applications.length) {
                ownerApplicationList.innerHTML = '<p class="text-slate-500">No pending clinic applications right now.</p>';
                return;
            }

            ownerApplicationList.innerHTML = applications.map((application) => `
                <div class="rounded-[2rem] border border-slate-100 p-5">
                    <div class="text-lg font-bold text-dark">${escapeOwnerHtml(application.clinic_name)}</div>
                    <div class="text-sm text-slate-500">${escapeOwnerHtml(application.area_name)} | ${escapeOwnerHtml(application.address)}</div>
                    <div class="text-xs text-slate-400 mt-2">${escapeOwnerHtml(application.admin_name)} | ${escapeOwnerHtml(application.admin_email)} | ${escapeOwnerHtml(application.admin_phone ?? '')}</div>
                    <div class="grid lg:grid-cols-2 gap-3 mt-4">
                        <form method="POST" action="${escapeOwnerHtml(application.approve_url)}" class="space-y-3">
                            @csrf
                            <input name="owner_notes" class="form-control" placeholder="Approval notes">
                            <button class="w-full bg-accent text-white py-3 rounded-2xl font-bold" type="submit">Approve Clinic</button>
                        </form>
                        <form method="POST" action="${escapeOwnerHtml(application.reject_url)}" class="space-y-3">
                            @csrf
                            <textarea name="owner_notes" class="form-control" rows="4" placeholder="Reason for rejection" required></textarea>
                            <button class="w-full bg-rose-600 text-white py-3 rounded-2xl font-bold" type="submit">Reject Application</button>
                        </form>
                    </div>
                </div>
            `).join('');
        }

        function renderOwnerClinics(clinics) {
            if (!ownerClinicList || !clinics) return;

            ownerClinicList.innerHTML = clinics.map((clinic) => `
                <div class="rounded-[2rem] border border-slate-100 p-5">
                    <div class="flex flex-wrap justify-between gap-4">
                        <div>
                            <div class="text-lg font-bold text-dark">${escapeOwnerHtml(clinic.name)}</div>
                            <div class="text-sm text-slate-500">${escapeOwnerHtml(clinic.area_name)} | ${escapeOwnerHtml(clinic.address)}</div>
                            <div class="text-xs text-slate-400 mt-2">Admins: ${escapeOwnerHtml(clinic.admin_count)} | Doctors: ${escapeOwnerHtml(clinic.doctor_count)}</div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex mt-2 px-3 py-1 rounded-full text-xs font-bold ${clinic.is_active ? 'bg-green-100 text-green-700' : 'bg-rose-100 text-rose-700'}">
                                ${clinic.is_active ? 'Active' : 'Inactive'}
                            </span>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function renderOwnerUsers(users, clinics, doctors) {
            if (!ownerUserList || !users || !clinics) return;

            const clinicOptions = (selectedClinic) => `
                <option value="">No clinic</option>
                ${clinics.map((clinic) => `
                    <option value="${clinic.id}" ${Number(selectedClinic) === Number(clinic.id) ? 'selected' : ''}>${escapeOwnerHtml(clinic.name)}</option>
                `).join('')}
            `;

            const doctorOptions = (selectedClinic, selectedDoctor) => `
                <option value="">No doctor assignment</option>
                ${(doctors || [])
                    .filter((doctor) => !selectedClinic || Number(doctor.clinic_id) === Number(selectedClinic))
                    .map((doctor) => `
                        <option value="${doctor.id}" data-clinic-id="${doctor.clinic_id}" ${Number(selectedDoctor) === Number(doctor.id) ? 'selected' : ''}>${escapeOwnerHtml(doctor.name)}</option>
                    `).join('')}
            `;

            ownerUserList.innerHTML = users.map((member) => {
                const actionUrl = ownerUserUpdateRouteTemplate.replace(/0$/, member.id);
                const disabledAttr = member.is_owner ? 'disabled' : '';
                const roleLabel = member.is_owner ? 'Owner' : String(member.role || '').replaceAll('_', ' ');
                const roleClass = {
                    super_admin: 'bg-slate-900 text-white',
                    admin: 'bg-blue-50 text-primary',
                    doctor: 'bg-purple-50 text-secondary',
                    helper: 'bg-green-50 text-accent',
                    patient: 'bg-slate-100 text-slate-600',
                }[member.role] || 'bg-slate-100 text-slate-600';
                const initial = escapeOwnerHtml(String(member.name || 'U').charAt(0).toUpperCase());

                return `
                    <form method="POST" action="${escapeOwnerHtml(actionUrl)}" class="owner-user-form dashboard-card rounded-[2rem] p-5">
                        @csrf
                        @method('PATCH')
                        <div class="grid xl:grid-cols-[1.1fr_1.5fr_auto] gap-5 items-center">
                            <div class="flex items-center gap-4 min-w-0">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary to-secondary text-white flex items-center justify-center text-xl font-black shrink-0">
                                    ${initial}
                                </div>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-lg font-black text-dark truncate">${escapeOwnerHtml(member.name)}</h3>
                                        <span class="rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-[0.14em] ${roleClass}">
                                            ${escapeOwnerHtml(roleLabel)}
                                        </span>
                                    </div>
                                    <p class="text-sm font-medium text-slate-500 truncate">${escapeOwnerHtml(member.email)}</p>
                                    <div class="mt-2 flex flex-wrap gap-2 text-[11px] font-bold">
                                        <span class="rounded-full bg-slate-50 px-3 py-1 text-slate-500">${escapeOwnerHtml(member.phone || 'No phone')}</span>
                                        <span class="rounded-full ${member.is_verified ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700'} px-3 py-1">${member.is_verified ? 'Verified' : 'Not verified'}</span>
                                        <span class="rounded-full bg-slate-50 px-3 py-1 text-slate-500">Joined ${escapeOwnerHtml(member.created_at || 'Unknown')}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-4 gap-3">
                                <input name="name" value="${escapeOwnerHtml(member.name)}" class="form-control" ${disabledAttr}>
                                <input name="phone" value="${escapeOwnerHtml(member.phone ?? '')}" class="form-control" placeholder="Phone" ${disabledAttr}>
                                <select name="role" class="form-control owner-role-select" ${disabledAttr}>
                                    ${member.is_owner ? `<option value="super_admin" selected>${roleLabel}</option>` : `
                                        <option value="admin" ${member.role === 'admin' ? 'selected' : ''}>Clinic Admin</option>
                                        <option value="doctor" ${member.role === 'doctor' ? 'selected' : ''}>Doctor</option>
                                        <option value="helper" ${member.role === 'helper' ? 'selected' : ''}>Doctor Helper</option>
                                        <option value="patient" ${member.role === 'patient' ? 'selected' : ''}>Patient</option>
                                    `}
                                </select>
                                <select name="clinic_id" class="form-control owner-clinic-select" ${disabledAttr}>
                                    ${clinicOptions(member.clinic_id)}
                                </select>
                                <select name="doctor_id" class="md:col-span-2 form-control owner-doctor-select" ${disabledAttr}>
                                    ${doctorOptions(member.clinic_id, member.doctor_id)}
                                </select>
                                <div class="md:col-span-2 rounded-2xl bg-slate-50 border border-slate-100 px-4 py-3 text-xs font-bold text-slate-500">
                                    Clinic: ${escapeOwnerHtml(member.clinic_name || 'None')}<br>
                                    Doctor: ${escapeOwnerHtml(member.assigned_doctor_name || 'None')}
                                </div>
                            </div>

                            <div class="flex xl:flex-col gap-2 justify-end">
                                ${member.is_owner
                                    ? '<span class="text-xs font-bold text-primary px-5 py-3">Protected owner</span>'
                                    : '<button class="bg-dark text-white px-5 py-3 rounded-2xl font-bold hover:bg-primary transition-colors" type="submit">Update</button>'
                                }
                            </div>
                        </div>
                    </form>
                `;
            }).join('');
            syncOwnerUserForms();
        }

        function syncOwnerUserForm(form) {
            const roleSelect = form.querySelector('.owner-role-select');
            const clinicSelect = form.querySelector('.owner-clinic-select');
            const doctorSelect = form.querySelector('.owner-doctor-select');
            if (!roleSelect || !clinicSelect || !doctorSelect) return;

            const role = roleSelect.value;
            const needsClinic = ['admin', 'doctor', 'helper'].includes(role);
            clinicSelect.required = needsClinic;

            if (role === 'patient') {
                clinicSelect.value = '';
            }

            doctorSelect.disabled = role !== 'helper';
            if (role !== 'helper') {
                doctorSelect.value = '';
                return;
            }

            const selectedClinicId = clinicSelect.value;
            Array.from(doctorSelect.options).forEach((option) => {
                const optionClinicId = option.dataset.clinicId || '';
                option.hidden = Boolean(option.value && selectedClinicId && optionClinicId !== selectedClinicId);
            });

            const selectedOption = doctorSelect.selectedOptions[0];
            if (selectedOption && selectedOption.hidden) {
                doctorSelect.value = '';
            }
        }

        function syncOwnerUserForms() {
            document.querySelectorAll('.owner-user-form').forEach(syncOwnerUserForm);
        }

        if (ownerUserList) {
            ownerUserList.addEventListener('focusin', () => {
                ownerUsersDirtyUntil = Date.now() + 60000;
            });
            ownerUserList.addEventListener('input', (event) => {
                ownerUsersDirtyUntil = Date.now() + 60000;
                const form = event.target.closest('.owner-user-form');
                if (form) syncOwnerUserForm(form);
            });
            ownerUserList.addEventListener('change', (event) => {
                ownerUsersDirtyUntil = Date.now() + 60000;
                const form = event.target.closest('.owner-user-form');
                if (form) syncOwnerUserForm(form);
            });
        }

        async function refreshOwnerStats() {
            const response = await fetch(ownerDashboardLiveUrl, {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) return;

            const data = await response.json();
            Object.entries(data.stats || {}).forEach(([key, value]) => {
                const node = document.querySelector(`[data-owner-live-stat="${key}"]`);
                if (node) node.innerText = value;
            });
            renderOwnerClinics(data.clinics || []);
            renderClinicApplications(data.clinic_applications || []);

            if (!ownerUserList || (!ownerUserList.contains(document.activeElement) && Date.now() > ownerUsersDirtyUntil)) {
                renderOwnerUsers(data.users || [], data.clinics || [], data.doctors || []);
            }
        }

        syncOwnerUserForms();
        refreshOwnerStats();
        setInterval(refreshOwnerStats, 5000);
    </script>
@endsection
