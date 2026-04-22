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
    <div class="grid md:grid-cols-5 gap-4 mb-8">
        @foreach ($stats as $label => $value)
            <div class="dashboard-card rounded-3xl p-6">
                <div class="text-xs uppercase tracking-widest text-slate-500 font-bold">{{ str_replace('_', ' ', $label) }}</div>
                <div class="text-4xl font-black text-gradient mt-3" data-owner-live-stat="{{ $label }}">{{ $value }}</div>
            </div>
        @endforeach
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
    <section id="owner-users" class="dashboard-card rounded-[2rem] p-8">
        <h2 class="text-2xl font-extrabold mb-6 text-dark">All Users</h2>
        <div class="space-y-4" id="owner-user-list">
            @foreach ($users as $member)
                <form method="POST" action="{{ route('owner.users.update', $member) }}" class="rounded-[2rem] border border-slate-100 p-5 grid lg:grid-cols-5 gap-3 items-center">
                    @csrf
                    @method('PATCH')
                    <input name="name" value="{{ $member->name }}" class="form-control" @disabled($member->isSuperAdmin())>
                    <input name="phone" value="{{ $member->phone }}" class="form-control" placeholder="Phone" @disabled($member->isSuperAdmin())>
                    <select name="role" class="form-control" @disabled($member->isSuperAdmin())>
                        @foreach (['admin' => 'Clinic Admin', 'doctor' => 'Doctor', 'patient' => 'Patient'] as $value => $label)
                            <option value="{{ $value }}" @selected($member->role === $value)>{{ $member->isSuperAdmin() ? 'Owner' : $label }}</option>
                        @endforeach
                    </select>
                    <select name="clinic_id" class="form-control" @disabled($member->isSuperAdmin())>
                        <option value="">No clinic</option>
                        @foreach ($clinics as $clinic)
                            <option value="{{ $clinic->id }}" @selected($member->clinic_id === $clinic->id)>{{ $clinic->name }}</option>
                        @endforeach
                    </select>
                    <div class="flex items-center justify-between gap-3">
                        <div class="text-xs text-slate-500 truncate">{{ $member->email }}</div>
                        @if (! $member->isSuperAdmin())
                            <button class="bg-dark text-white px-5 py-3 rounded-2xl font-bold" type="submit">Update</button>
                        @else
                            <span class="text-xs font-bold text-primary">Owner</span>
                        @endif
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

    <script>
        const ownerDashboardLiveUrl = "{{ route('dashboard.live') }}";
        const ownerUserList = document.getElementById('owner-user-list');
        const ownerClinicList = document.getElementById('owner-clinic-list');
        const ownerApplicationList = document.getElementById('owner-application-list');
        const ownerUserUpdateRouteTemplate = "{{ route('owner.users.update', 0) }}";

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

        function renderOwnerUsers(users, clinics) {
            if (!ownerUserList || !users || !clinics) return;

            const clinicOptions = (selectedClinic) => `
                <option value="">No clinic</option>
                ${clinics.map((clinic) => `
                    <option value="${clinic.id}" ${Number(selectedClinic) === Number(clinic.id) ? 'selected' : ''}>${escapeOwnerHtml(clinic.name)}</option>
                `).join('')}
            `;

            ownerUserList.innerHTML = users.map((member) => {
                const actionUrl = ownerUserUpdateRouteTemplate.replace(/0$/, member.id);
                const disabledAttr = member.is_owner ? 'disabled' : '';
                const roleLabel = member.is_owner ? 'Owner' : member.role;

                return `
                    <form method="POST" action="${escapeOwnerHtml(actionUrl)}" class="rounded-[2rem] border border-slate-100 p-5 grid lg:grid-cols-5 gap-3 items-center">
                        @csrf
                        @method('PATCH')
                        <input name="name" value="${escapeOwnerHtml(member.name)}" class="form-control" ${disabledAttr}>
                        <input name="phone" value="${escapeOwnerHtml(member.phone ?? '')}" class="form-control" placeholder="Phone" ${disabledAttr}>
                        <select name="role" class="form-control" ${disabledAttr}>
                            ${member.is_owner ? `<option value="super_admin" selected>${roleLabel}</option>` : `
                                <option value="admin" ${member.role === 'admin' ? 'selected' : ''}>Clinic Admin</option>
                                <option value="doctor" ${member.role === 'doctor' ? 'selected' : ''}>Doctor</option>
                                <option value="patient" ${member.role === 'patient' ? 'selected' : ''}>Patient</option>
                            `}
                        </select>
                        <select name="clinic_id" class="form-control" ${disabledAttr}>
                            ${clinicOptions(member.clinic_id)}
                        </select>
                        <div class="flex items-center justify-between gap-3">
                            <div class="text-xs text-slate-500 truncate">${escapeOwnerHtml(member.email)}</div>
                            ${member.is_owner
                                ? '<span class="text-xs font-bold text-primary">Owner</span>'
                                : '<button class="bg-dark text-white px-5 py-3 rounded-2xl font-bold" type="submit">Update</button>'
                            }
                        </div>
                    </form>
                `;
            }).join('');
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

            if (!ownerUserList || !ownerUserList.contains(document.activeElement)) {
                renderOwnerUsers(data.users || [], data.clinics || []);
            }
        }

        refreshOwnerStats();
        setInterval(refreshOwnerStats, 5000);
    </script>
@endsection
