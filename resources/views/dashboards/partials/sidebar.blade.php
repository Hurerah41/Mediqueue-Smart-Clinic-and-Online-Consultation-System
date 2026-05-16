@php
    $user = auth()->user();
    $roleLabel = match ($user->role) {
        \App\Models\User::ROLE_SUPER_ADMIN => 'Platform Owner',
        \App\Models\User::ROLE_ADMIN => 'Clinic Admin',
        \App\Models\User::ROLE_DOCTOR => 'Doctor',
        \App\Models\User::ROLE_HELPER => 'Doctor Helper',
        default => 'Patient',
    };

    $links = match ($user->role) {
        \App\Models\User::ROLE_SUPER_ADMIN => [
            ['label' => 'Overview', 'icon' => 'ph-squares-four', 'href' => route('owner.overview'), 'active' => request()->routeIs('owner.overview')],
            ['label' => 'Clinics', 'icon' => 'ph-hospital', 'href' => route('owner.clinics'), 'active' => request()->routeIs('owner.clinics')],
            ['label' => 'Applications', 'icon' => 'ph-clipboard-text', 'href' => route('owner.applications'), 'active' => request()->routeIs('owner.applications')],
            ['label' => 'Users', 'icon' => 'ph-users-three', 'href' => route('owner.users'), 'active' => request()->routeIs('owner.users')],
            ['label' => 'Settings', 'icon' => 'ph-gear-six', 'href' => route('owner.settings'), 'active' => request()->routeIs('owner.settings')],
        ],
        \App\Models\User::ROLE_ADMIN => [
            ['label' => 'Reports', 'icon' => 'ph-chart-line-up', 'href' => route('admin.reports'), 'active' => request()->routeIs('admin.reports')],
            ['label' => 'Doctors', 'icon' => 'ph-stethoscope', 'href' => route('admin.doctors.index'), 'active' => request()->routeIs('admin.doctors.index')],
            ['label' => 'Settings', 'icon' => 'ph-gear-six', 'href' => route('admin.settings'), 'active' => request()->routeIs('admin.settings')],
        ],
        \App\Models\User::ROLE_DOCTOR => [
            ['label' => 'Queue Board', 'icon' => 'ph-queue', 'href' => route('doctor.queue'), 'active' => request()->routeIs('doctor.queue')],
            ['label' => 'Prescription Desk', 'icon' => 'ph-pill', 'href' => route('doctor.prescriptions'), 'active' => request()->routeIs('doctor.prescriptions')],
        ],
        \App\Models\User::ROLE_HELPER => [
            ['label' => 'Helper Desk', 'icon' => 'ph-first-aid-kit', 'href' => route('helper.dashboard'), 'active' => request()->routeIs('helper.dashboard')],
        ],
        default => [
            ['label' => 'Appointments', 'icon' => 'ph-calendar-check', 'href' => route('patient.appointments'), 'active' => request()->routeIs('patient.appointments')],
            ['label' => 'Book Token', 'icon' => 'ph-ticket', 'href' => route('clinics.index'), 'active' => request()->routeIs('clinics.*')],
            ['label' => 'AI Tools', 'icon' => 'ph-sparkle', 'href' => route('patient.ai-tools'), 'active' => request()->routeIs('patient.ai-tools')],
            ['label' => 'Platform Reviews', 'icon' => 'ph-star-half', 'href' => route('patient.reviews'), 'active' => request()->routeIs('patient.reviews')],
        ],
    };
@endphp

<aside class="dashboard-sidebar-card rounded-[1.75rem] p-4 lg:sticky lg:top-28">
    <div class="flex items-center justify-between gap-3 pb-5 border-b border-slate-100">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-11 h-11 rounded-2xl bg-primary text-white flex items-center justify-center text-xl font-black shrink-0 shadow-glow-primary">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <h2 class="text-base font-extrabold text-dark leading-tight truncate">{{ $user->name }}</h2>
                <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400 font-black">{{ $roleLabel }}</p>
            </div>
        </div>
        <button type="button" id="dashboard-sidebar-toggle" class="lg:hidden w-10 h-10 rounded-2xl bg-slate-50 text-dark border border-slate-100 flex items-center justify-center text-2xl">
            <i class="ph-bold ph-list"></i>
        </button>
    </div>

    <div id="dashboard-sidebar-body" class="hidden lg:block">
    <nav class="mt-5 space-y-2">
        @foreach ($links as $link)
            <a
                href="{{ $link['href'] }}"
                class="flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-extrabold transition-all {{ $link['active'] ? 'bg-primary text-white shadow-[0_16px_32px_-18px_rgba(37,99,235,0.9)]' : 'text-slate-500 hover:bg-slate-50 hover:text-primary' }}"
            >
                <i class="ph-fill {{ $link['icon'] }} text-lg"></i>
                {{ $link['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="mt-6 rounded-[1.5rem] bg-slate-50 border border-slate-100 p-5">
        <p class="text-[11px] uppercase tracking-[0.18em] text-primary font-black">Quick Tip</p>
        <p class="text-sm text-slate-500 mt-3 leading-relaxed font-medium">Use this sidebar to jump between key dashboard sections and manage your queue faster.</p>
    </div>
    </div>
</aside>

<script>
    (() => {
        const toggleButton = document.getElementById('dashboard-sidebar-toggle');
        const sidebarBody = document.getElementById('dashboard-sidebar-body');

        if (!toggleButton || !sidebarBody) {
            return;
        }

        toggleButton.addEventListener('click', () => {
            const isHidden = sidebarBody.classList.toggle('hidden');
            toggleButton.innerHTML = isHidden
                ? '<i class="ph-bold ph-list"></i>'
                : '<i class="ph-bold ph-x"></i>';
        });

        sidebarBody.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    sidebarBody.classList.add('hidden');
                    toggleButton.innerHTML = '<i class="ph-bold ph-list"></i>';
                }
            });
        });
    })();
</script>
