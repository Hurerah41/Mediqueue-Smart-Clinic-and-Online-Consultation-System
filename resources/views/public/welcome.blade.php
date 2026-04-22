@extends('layouts.app', ['title' => 'MediQueue | Smart Healthcare & Queue Management'])

@section('content')
    @php
        $featuredClinics = $clinics->take(3)->values();
        $clinicImages = [
            'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?q=80&w=900&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1586773860418-d37222d8fce3?q=80&w=900&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1538108149393-cebb47cbdc41?q=80&w=900&auto=format&fit=crop',
        ];
    @endphp

    <section id="home" class="relative overflow-hidden pt-4 pb-14">
        <div class="relative z-10 w-[97%] mx-auto">
            <div class="max-w-5xl mx-auto text-center reveal">
                <div class="inline-flex items-center gap-2 rounded-full border border-slate-200/70 bg-white/70 px-3 py-1.5 text-xs font-bold text-slate-600 shadow-sm backdrop-blur-xl">
                    <span class="h-2 w-2 rounded-full bg-accent animate-pulse-soft shadow-[0_0_14px_rgba(34,197,94,0.75)]"></span>
                    Bridging digital and physical care
                    <i class="ph-bold ph-arrow-right text-slate-400"></i>
                </div>

                <h1 class="mt-7 text-5xl sm:text-6xl lg:text-7xl font-black tracking-[-0.055em] leading-[0.95] text-dark tracking-[1em])">
                    Optimize Your Queue <br class="hidden sm:block">
                    Management with <span class="text-gradient-brand">MediQueue</span>
                </h1>

                <p class="mt-6 max-w-2xl mx-auto text-base sm:text-lg leading-8 text-slate-500 font-medium">
                    A real-time healthcare SaaS platform for Karachi clinics and patients: find verified doctors, book queue tokens, track live wait times, receive smart alerts, and download prescriptions digitally.
                </p>

                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="{{ route('clinics.index') }}" class="shine-button bg-dark text-white px-8 py-3.5 rounded-full text-sm sm:text-base font-black shadow-lg shadow-dark/10 hover:shadow-glow-primary transition-all w-full sm:w-auto">Find Clinics</a>
                    <a href="{{ route('clinic-applications.create') }}" class="bg-white/70 backdrop-blur-xl border border-slate-200 px-8 py-3.5 rounded-full text-sm sm:text-base font-black text-dark hover:border-primary/40 hover:bg-white transition-all w-full sm:w-auto inline-flex items-center justify-center gap-2">
                        <i class="ph-fill ph-buildings text-lg text-primary"></i> Register Your Clinic
                    </a>
                </div>
            </div>

            <div class="mt-14 grid grid-cols-1 md:grid-cols-4 gap-4 lg:gap-5">
                <div class="hidden md:flex flex-col gap-4">
                    <div class="bento-card rounded-[1.75rem] p-5 reveal reveal-delay-1">
                        <div class="flex items-start justify-between">
                            <div class="w-10 h-10 rounded-2xl bg-blue-50 text-primary flex items-center justify-center"><i class="ph-fill ph-clock text-xl"></i></div>
                            <span class="h-2.5 w-2.5 rounded-full bg-accent shadow-[0_0_12px_rgba(34,197,94,0.7)]"></span>
                        </div>
                        <p class="mt-5 text-3xl font-black text-dark tracking-tight">14 mins</p>
                        <p class="mt-1 text-[11px] uppercase tracking-[0.2em] font-black text-slate-400">Avg Wait Time</p>
                    </div>

                    <div class="bento-card rounded-[1.75rem] p-5 reveal reveal-delay-2">
                        <div class="flex items-center justify-between">
                            <div class="w-10 h-10 rounded-2xl bg-blue-50 text-primary flex items-center justify-center"><i class="ph-fill ph-chart-line text-xl"></i></div>
                            <span class="rounded-lg bg-blue-50 px-2.5 py-1 text-[10px] font-black text-primary">#A-42</span>
                        </div>
                        <h3 class="mt-4 text-sm font-black text-dark">Patient Volume</h3>
                        <div class="relative mt-4 h-12 overflow-hidden">
                            <svg class="absolute inset-0 h-full w-full text-primary" viewBox="0 0 120 48" preserveAspectRatio="none" aria-hidden="true">
                                <path d="M2 36 C20 42, 30 10, 48 24 S76 42, 94 16 S112 12, 118 8" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2 bento-card rounded-[2.25rem] p-5 sm:p-6 shadow-float relative overflow-hidden reveal">
                    <div class="absolute -right-24 -top-24 w-64 h-64 rounded-full bg-primary/10 blur-3xl"></div>
                    <div class="relative z-10">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-white/60">
                            <div class="flex items-center gap-3">
                                <span class="w-2.5 h-7 rounded-full bg-primary"></span>
                                <div>
                                    <p class="text-[11px] uppercase tracking-[0.22em] font-black text-slate-400">Queue Command Center</p>
                                    <h2 class="mt-1 text-xl font-black text-dark">Live patient flow</h2>
                                </div>
                            </div>
                            <div class="flex gap-2 text-[10px] font-black text-slate-500">
                                <span class="rounded-xl bg-white/80 border border-slate-200/70 px-3 py-2">Dr. Ahmed</span>
                                <span class="rounded-xl bg-white/80 border border-slate-200/70 px-3 py-2">Today</span>
                            </div>
                        </div>

                        <div class="mt-6 grid gap-6 md:grid-cols-[1fr_0.85fr] items-center">
                            <div>
                                <p class="text-[11px] uppercase tracking-[0.22em] font-black text-slate-400">Currently Serving</p>
                                <h3 class="mt-3 text-6xl sm:text-7xl font-black tracking-[-0.08em] text-dark">#A-38</h3>
                                <p class="mt-3 text-sm leading-7 font-medium text-slate-500">Doctor called the active token. Patients near their turn are notified instantly.</p>
                                <div class="mt-5 flex items-center gap-2">
                                    <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-[11px] font-black text-emerald-600">Live queue</span>
                                    <span class="rounded-full bg-blue-50 px-3 py-1.5 text-[11px] font-black text-primary">3 ahead</span>
                                </div>
                            </div>

                            <div class="relative mx-auto w-52 h-52">
                                <svg viewBox="0 0 180 180" class="w-full h-full animate-[spin_12s_linear_infinite]" aria-hidden="true">
                                    <circle cx="90" cy="90" r="70" fill="none" stroke="#E2E8F0" stroke-width="12" />
                                    <circle cx="90" cy="90" r="70" fill="none" stroke="url(#queueRing)" stroke-width="12" stroke-linecap="round" stroke-dasharray="330 440" />
                                    <defs><linearGradient id="queueRing" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#2563EB" /><stop offset="1" stop-color="#7C3AED" /></linearGradient></defs>
                                </svg>
                                <div class="absolute inset-8 rounded-full bg-white/80 border border-white shadow-soft flex flex-col items-center justify-center text-center">
                                    <span class="text-3xl font-black text-dark">82%</span>
                                    <span class="text-[10px] uppercase tracking-[0.18em] font-black text-slate-400 mt-1">Flow Health</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hidden md:flex flex-col gap-4">
                    <div class="bento-card rounded-[1.75rem] p-5 reveal reveal-delay-1">
                        <div class="flex items-center justify-between">
                            <p class="text-[11px] uppercase tracking-[0.2em] font-black text-slate-400">Revenue</p>
                            <span class="rounded-full bg-emerald-50 px-2 py-1 text-[10px] font-black text-emerald-600">+8.2%</span>
                        </div>
                        <p class="mt-4 text-3xl font-black text-dark">Rs 42k</p>
                    </div>

                    <div class="bento-card rounded-[1.75rem] p-5 flex-1 reveal reveal-delay-3">
                        <div class="flex items-center justify-between">
                            <div class="w-10 h-10 rounded-2xl bg-purple-50 text-secondary flex items-center justify-center"><i class="ph-fill ph-calendar-plus text-xl"></i></div>
                            <span class="rounded-xl bg-white/80 border border-slate-200 px-2.5 py-1 text-[10px] font-black text-slate-500">Weekly</span>
                        </div>
                        <div class="mt-5 grid grid-cols-6 gap-2">
                            @foreach ([0.25, 0.55, 0.85, 1, 0.7, 0.35, 0.3, 0.8, 0.95, 0.65, 0.45, 0.2] as $dot)
                                <span class="aspect-square rounded-full {{ $dot > 0.9 ? 'bg-secondary shadow-[0_0_10px_rgba(124,58,237,0.35)]' : ($dot > 0.65 ? 'bg-primary' : ($dot > 0.4 ? 'bg-primary/40' : 'bg-slate-200')) }}"></span>
                            @endforeach
                        </div>
                        <p class="mt-5 text-[11px] uppercase tracking-[0.2em] font-black text-slate-400">Weekly Patients</p>
                        <p class="mt-1 text-2xl font-black text-dark">842</p>
                    </div>
                </div>
            </div>

            <div class="mt-9 max-w-4xl mx-auto reveal reveal-delay-2">
                <form method="GET" action="{{ route('clinics.index') }}" class="bento-card rounded-[1.75rem] p-2 flex flex-col md:flex-row gap-2">
                    <label class="flex-1 bg-white/70 rounded-[1.25rem] px-4 py-3 flex items-center gap-3 border border-white/70">
                        <i class="ph ph-map-pin text-slate-400 text-xl"></i>
                        <select name="area_id" class="w-full bg-transparent outline-none text-dark text-sm font-bold appearance-none cursor-pointer">
                            <option value="">Area</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area->id }}">{{ $area->name }}, {{ $area->city }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="flex-1 bg-white/70 rounded-[1.25rem] px-4 py-3 flex items-center gap-3 border border-white/70">
                        <i class="ph ph-stethoscope text-slate-400 text-xl"></i>
                        <select name="specialization_id" class="w-full bg-transparent outline-none text-dark text-sm font-bold appearance-none cursor-pointer">
                            <option value="">Speciality</option>
                            @foreach ($specializations as $specialization)
                                <option value="{{ $specialization->id }}">{{ $specialization->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <button type="submit" class="shine-button bg-dark text-white rounded-[1.25rem] px-7 py-3.5 text-sm font-black hover:bg-primary transition-colors">Find Clinics</button>
                </form>
            </div>
        </div>
    </section>

    <section id="how-it-works" class="process-gradient py-16 lg:py-20 rounded-[2.5rem]">
        <div class="w-[97%] mx-auto">
            <div class="text-center max-w-3xl mx-auto reveal">
                <p class="text-[11px] uppercase tracking-[0.26em] font-black text-primary">Simple process</p>
                <h2 class="mt-3 text-3xl sm:text-5xl font-black tracking-tight text-dark">Your health, streamlined in three steps.</h2>
            </div>
            <div class="mt-10 grid gap-5 md:grid-cols-3">
                @foreach ([
                    ['1', 'ph-map-pin', '#2563EB', '#EFF6FF', 'Find Nearby Clinic', 'Locate top-rated clinics and specialists in your area based on real-time availability and wait times.'],
                    ['2', 'ph-ticket', '#7C3AED', '#F5F3FF', 'Book Your Token', 'Reserve your spot in the queue from your phone. Track live progress and know exactly when to leave.'],
                    ['3', 'ph-stethoscope', '#22C55E', '#ECFDF5', 'Visit or Consult', 'Walk in right on time without waiting, or join a secure high-definition video consultation from home.'],
                ] as $step)
                    <div class="relative overflow-hidden rounded-[2rem] bg-white p-7 shadow-soft border border-slate-100 reveal">
                        <span class="absolute right-5 top-2 text-8xl font-black text-slate-100">{{ $step[0] }}</span>
                        <div class="relative z-10">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background-color: {{ $step[3] }}; color: {{ $step[2] }};"><i class="ph-fill {{ $step[1] }} text-2xl"></i></div>
                            <h3 class="mt-6 text-xl font-black text-dark">{{ $step[4] }}</h3>
                            <p class="mt-3 text-sm leading-7 font-medium text-slate-500">{{ $step[5] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="features" class="dark-dot-grid py-16 lg:py-24 rounded-[2.5rem] overflow-hidden">
        <div class="w-[97%] mx-auto">
            <div class="text-center max-w-4xl mx-auto mb-12 reveal">
                <p class="text-[11px] uppercase tracking-[0.28em] font-black text-blue-400">Powerful features</p>
                <h2 class="mt-4 text-3xl sm:text-5xl font-black tracking-tight text-white">Everything you need for seamless healthcare.</h2>
            </div>

            <div class="grid gap-5 lg:grid-cols-6">
                <div class="dark-feature-card rounded-[2rem] p-7 lg:col-span-2 reveal">
                    <div class="w-12 h-12 rounded-2xl bg-blue-950/80 text-blue-400 flex items-center justify-center"><i class="ph-fill ph-clock text-2xl"></i></div>
                    <h3 class="mt-6 text-xl font-black text-white">Live Queue Management</h3>
                    <p class="mt-3 text-sm leading-7 font-medium text-slate-300">Monitor your position in real-time. Our predictive algorithm provides highly accurate estimated wait times.</p>
                </div>

                <div class="dark-feature-card rounded-[2rem] p-7 lg:col-span-2 reveal reveal-delay-1">
                    <div class="w-12 h-12 rounded-2xl bg-purple-950/80 text-purple-300 flex items-center justify-center"><i class="ph-fill ph-video-camera text-2xl"></i></div>
                    <h3 class="mt-6 text-xl font-black text-white">HD Video Consultations</h3>
                    <p class="mt-3 text-sm leading-7 font-medium text-slate-300">Connect with specialists remotely via end-to-end encrypted video calls right from your dashboard.</p>
                </div>

                <div class="dark-feature-card rounded-[2rem] p-7 lg:col-span-2 reveal reveal-delay-2">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-950/80 text-emerald-300 flex items-center justify-center"><i class="ph-fill ph-folder-notch-open text-2xl"></i></div>
                    <h3 class="mt-6 text-xl font-black text-white">Digital Health Records</h3>
                    <p class="mt-3 text-sm leading-7 font-medium text-slate-300">Access your complete medical history, prescriptions, and lab reports securely in one central location.</p>
                </div>

                <div class="dark-feature-card rounded-[2rem] p-7 lg:col-span-4 grid gap-7 md:grid-cols-[1fr_0.85fr] items-center reveal">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-orange-950/80 text-orange-300 flex items-center justify-center"><i class="ph-fill ph-bell-ringing text-2xl"></i></div>
                        <h3 class="mt-6 text-2xl font-black text-white">Smart Push Notifications</h3>
                        <p class="mt-3 text-sm leading-7 font-medium text-slate-300">Never miss a turn. Get automated SMS and app alerts when your queue number is approaching, when reports are ready, or for medication reminders.</p>
                    </div>
                    <div class="rounded-[1.5rem] bg-[#081024]/90 border border-blue-400/20 p-5 shadow-[0_0_38px_-20px_rgba(37,99,235,0.75)]">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-blue-500/15 text-blue-300 flex items-center justify-center shrink-0"><i class="ph-fill ph-chat-circle-text text-xl"></i></div>
                            <div>
                                <p class="text-sm font-black text-white">MediQueue Alert</p>
                                <p class="mt-2 text-xs leading-6 font-medium text-slate-300">Your token #A-42 is next. Please proceed to Dr. Sarah's cabin.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dark-feature-card rounded-[2rem] p-7 lg:col-span-2 reveal reveal-delay-2 border-purple-400/30 shadow-[0_0_45px_-24px_rgba(124,58,237,0.95)]">
                    <div class="w-12 h-12 rounded-2xl bg-pink-950/80 text-pink-300 flex items-center justify-center"><i class="ph-fill ph-credit-card text-2xl"></i></div>
                    <h3 class="mt-6 text-xl font-black text-white">Seamless Payments</h3>
                    <p class="mt-3 text-sm leading-7 font-medium text-slate-300">Pay consultation fees instantly via Stripe, Apple Pay, or standard cards before your visit.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="ai-section" class="py-16">
        <div class="w-[97%] mx-auto reveal">
            <div class="bento-card rounded-[2.5rem] p-7 sm:p-9 lg:p-12 grid gap-8 lg:grid-cols-[1.05fr_0.95fr] items-center overflow-hidden relative">
                <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-secondary/10 blur-3xl"></div>
                <div class="relative z-10">
                    <p class="text-[11px] uppercase tracking-[0.26em] font-black text-primary">AI support</p>
                    <h2 class="mt-3 text-3xl sm:text-5xl font-black tracking-tight text-dark">Helpful AI support without replacing real doctors.</h2>
                    <p class="mt-4 text-sm sm:text-base leading-8 font-medium text-slate-500">
                        MediQueue can guide patients through common questions, route them toward the right specialty, and reduce repetitive clinic admin work while keeping medical decisions with qualified professionals.
                    </p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <span class="rounded-full bg-white/80 border border-slate-200 px-4 py-2 text-sm font-black text-slate-600">Symptom guidance</span>
                        <span class="rounded-full bg-white/80 border border-slate-200 px-4 py-2 text-sm font-black text-slate-600">Smart routing</span>
                        <span class="rounded-full bg-white/80 border border-slate-200 px-4 py-2 text-sm font-black text-slate-600">Patient Q&A</span>
                    </div>
                </div>
                <div class="relative z-10 grid gap-4">
                    <div class="rounded-[1.5rem] bg-white/80 border border-slate-100 p-5 shadow-sm flex items-center gap-4">
                        <div class="w-11 h-11 rounded-2xl bg-blue-50 text-primary flex items-center justify-center"><i class="ph-fill ph-robot text-xl"></i></div>
                        <div><p class="text-sm font-black text-dark">Patient assistant</p><p class="text-xs font-medium text-slate-500">Answers common booking and clinic questions</p></div>
                    </div>
                    <div class="rounded-[1.5rem] bg-white/80 border border-slate-100 p-5 shadow-sm flex items-center gap-4">
                        <div class="w-11 h-11 rounded-2xl bg-purple-50 text-secondary flex items-center justify-center"><i class="ph-fill ph-sparkle text-xl"></i></div>
                        <div><p class="text-sm font-black text-dark">Smarter intake</p><p class="text-xs font-medium text-slate-500">Collects context before the patient reaches the clinic</p></div>
                    </div>
                    <div class="rounded-[1.5rem] bg-white/80 border border-slate-100 p-5 shadow-sm flex items-center gap-4">
                        <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-accent flex items-center justify-center"><i class="ph-fill ph-shield-check text-xl"></i></div>
                        <div><p class="text-sm font-black text-dark">Human-first workflow</p><p class="text-xs font-medium text-slate-500">AI assists, doctors stay in control</p></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="clinics" class="relative py-16 lg:py-24 bg-[#F8FAFC] overflow-hidden">
        <div class="absolute -top-24 right-0 h-96 w-96 rounded-full bg-blue-600/5 blur-[120px] pointer-events-none"></div>

        <div class="relative z-10 w-[97%] mx-auto">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-10 reveal">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-white border border-slate-200 px-3 py-1.5 text-[10px] uppercase tracking-[0.22em] font-black text-slate-500 shadow-sm">
                        <i class="ph-fill ph-map-pin text-primary text-sm"></i>
                        Explore Local Care
                    </div>
                    <h2 class="mt-4 text-3xl sm:text-5xl font-black tracking-tight text-dark">
                        Discover Premium <span class="text-gradient-brand">Clinics Near You.</span>
                    </h2>
                </div>

                <div class="flex gap-3">
                    <button type="button" data-clinic-scroll="left" class="group/arrow w-12 h-12 rounded-full bg-white border border-slate-200 text-slate-600 flex items-center justify-center shadow-sm hover:border-primary hover:text-primary transition-all duration-300" aria-label="Scroll clinics left">
                        <i class="ph-bold ph-arrow-left transition-transform duration-300 group-hover/arrow:-translate-x-1"></i>
                    </button>
                    <button type="button" data-clinic-scroll="right" class="group/arrow w-12 h-12 rounded-full bg-white border border-slate-200 text-slate-600 flex items-center justify-center shadow-sm hover:border-primary hover:text-primary transition-all duration-300" aria-label="Scroll clinics right">
                        <i class="ph-bold ph-arrow-right transition-transform duration-300 group-hover/arrow:translate-x-1"></i>
                    </button>
                </div>
            </div>

            <div id="premium-clinic-carousel" class="flex overflow-x-auto snap-x snap-mandatory hide-scrollbar gap-6 pb-8 scroll-smooth reveal reveal-delay-1">
                @forelse ($featuredClinics as $clinic)
                    @php
                        $primaryColor = $clinic->brand_primary_color ?? '#2563EB';
                        $secondaryColor = $clinic->brand_secondary_color ?? '#7C3AED';
                        $clinicInitials = collect(explode(' ', $clinic->name))->filter()->take(2)->map(fn ($word) => strtoupper(substr($word, 0, 1)))->implode('');
                        $clinicRating = $clinic->doctors->avg(fn ($doctor) => $doctor->reviews->avg('rating') ?? 4.8) ?: 4.8;
                        $isOpen = $clinic->isOpenAt(now());
                        $specialtyTags = $clinic->doctors
                            ->pluck('specialization.name')
                            ->filter()
                            ->unique()
                            ->take(3)
                            ->values();
                        $doctorCount = $clinic->doctors->count();
                        $doctorLabel = $doctorCount === 1 ? '1 doctor available' : $doctorCount.' doctors available';
                    @endphp

                    <div class="group snap-start shrink-0 w-[360px] rounded-[2.5rem] p-3 bg-gradient-to-br from-white/85 to-white/55 backdrop-blur-2xl border border-white/60 shadow-[0_28px_70px_-45px_rgba(15,23,42,0.42),inset_0_1px_0_rgba(255,255,255,0.92),inset_0_0_0_1px_rgba(255,255,255,0.45)] transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] hover:-translate-y-2 hover:shadow-[0_38px_90px_-42px_rgba(37,99,235,0.45),inset_0_1px_0_rgba(255,255,255,1)]">
                        <div class="relative h-52 rounded-[2rem] overflow-hidden bg-slate-100">
                            @if ($clinic->logo_path)
                                <img src="{{ asset('storage/'.$clinic->logo_path) }}" alt="{{ $clinic->name }} image" class="w-full h-full object-cover transition-transform duration-700 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:scale-110">
                            @else
                                <div class="w-full h-full transition-transform duration-700 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:scale-110" style="background: linear-gradient(135deg, {{ $primaryColor }}18, {{ $secondaryColor }}32);">
                                    <div class="absolute inset-0 bg-grid-pattern opacity-40"></div>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="w-24 h-24 rounded-[1.8rem] flex items-center justify-center text-3xl font-black text-white shadow-soft" style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});">
                                            {{ $clinicInitials ?: 'MQ' }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-dark/80 via-dark/20 to-transparent opacity-80 transition-opacity duration-500 group-hover:opacity-95"></div>

                            <div class="absolute top-4 left-4 rounded-full bg-white/95 backdrop-blur-md px-3 py-1.5 text-[10px] font-black text-dark flex items-center gap-2 shadow-sm">
                                <span class="h-2 w-2 rounded-full {{ $isOpen ? 'bg-emerald-500 animate-pulse-soft shadow-[0_0_12px_rgba(34,197,94,0.75)]' : 'bg-slate-400' }}"></span>
                                {{ $isOpen ? 'Open Now' : 'Closed' }}
                            </div>

                            <div class="absolute top-4 right-4 bg-white/95 backdrop-blur-md px-3 py-1.5 rounded-full text-[11px] font-black text-dark flex items-center gap-1 shadow-sm">
                                <i class="ph-fill ph-star text-amber-400"></i>
                                {{ number_format($clinicRating, 1) }}
                            </div>

                            <div class="absolute left-4 right-4 bottom-4 flex items-end justify-between gap-3">
                                <div>
                                    <p class="text-xs font-bold text-white/75">{{ $clinic->area->name ?? 'Karachi' }}</p>
                                    <p class="mt-1 text-lg font-black text-white leading-tight">{{ $clinic->name }}</p>
                                </div>
                                @if ($clinic->logo_path)
                                    <img src="{{ asset('storage/'.$clinic->logo_path) }}" alt="{{ $clinic->name }} logo" class="w-12 h-12 rounded-2xl object-cover border-2 border-white shadow-lg bg-white">
                                @else
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-sm font-black text-white border-2 border-white shadow-lg" style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});">{{ $clinicInitials ?: 'MQ' }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="px-3 pt-5 pb-3 flex min-h-[17.5rem] flex-col">
                            <h3 class="text-2xl font-black tracking-tight text-dark transition-colors duration-300 group-hover:text-primary">{{ $clinic->name }}</h3>
                            <p class="mt-2 flex items-center gap-2 text-sm font-semibold text-slate-500">
                                <i class="ph-fill ph-map-pin text-slate-400"></i>
                                {{ \Illuminate\Support\Str::limit($clinic->address, 48) }}
                            </p>

                            <div class="mt-4 flex flex-wrap gap-2">
                                @forelse ($specialtyTags as $tag)
                                    <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-[11px] font-black text-slate-600">{{ $tag }}</span>
                                @empty
                                    <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-[11px] font-black text-slate-600">General Care</span>
                                    <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-[11px] font-black text-slate-600">Walk-in</span>
                                @endforelse
                            </div>

                            <div class="mt-auto border-t border-slate-100 pt-5">
                                <div class="flex items-end justify-between gap-4">
                                    <div>
                                        <p class="text-[10px] uppercase tracking-[0.2em] font-black text-slate-400">Next Available</p>
                                        <p class="mt-1 flex items-center gap-1.5 text-sm font-black {{ $isOpen ? 'text-primary' : 'text-slate-500' }}">
                                            <i class="ph-fill ph-clock"></i>
                                            {{ $clinic->nextAvailabilityLabel(now()) }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] uppercase tracking-[0.2em] font-black text-slate-400">Doctors</p>
                                        <p class="mt-1 text-sm font-black text-dark">{{ $doctorLabel }}</p>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('clinics.show', $clinic) }}" class="shine-button rounded-full bg-dark px-5 py-3 text-xs font-black text-white transition-all duration-300 hover:bg-primary">
                                        View Clinic
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bento-card rounded-[2rem] p-8 w-full text-center"><p class="text-lg font-black text-dark">No approved clinics yet</p><p class="mt-2 text-sm text-slate-500 font-medium">Approved clinics will appear here automatically.</p></div>
                @endforelse
            </div>
        </div>
    </section>

    <section id="faq" class="py-16 lg:py-20 bg-white/80 border-y border-slate-200/60">
        <div class="max-w-3xl mx-auto px-4">
            <div class="text-center mb-10 reveal">
                <p class="text-[11px] uppercase tracking-[0.26em] font-black text-primary">FAQ</p>
                <h2 class="mt-3 text-3xl sm:text-4xl font-black tracking-tight text-dark">Got questions?</h2>
            </div>
            <div class="space-y-4 reveal reveal-delay-1">
                @foreach ([
                    ['Is MediQueue free for patients?', 'Yes. Patients can create an account, find clinics, and track queue status. Consultation fees are paid according to the clinic or doctor policy.'],
                    ['Can I book outside clinic hours?', 'No. If a clinic is closed, booking is blocked until the clinic is available again based on its approved timings.'],
                    ['How do clinics appear on MediQueue?', 'A clinic submits the registration form. The super admin reviews the details, verifies the clinic, and approves it before it appears publicly.'],
                    ['Do patients receive real-time alerts?', 'Yes. Queue-near, doctor-called, visit-completed, and prescription-ready notifications are handled through the platform.'],
                ] as $faq)
                    <div class="rounded-[1.5rem] bg-white border border-slate-200 shadow-sm overflow-hidden">
                        <button type="button" class="home-faq-btn w-full px-6 py-5 flex items-center justify-between gap-4 text-left"><span class="text-sm sm:text-base font-black text-dark">{{ $faq[0] }}</span><span class="w-9 h-9 rounded-full bg-slate-50 flex items-center justify-center shrink-0"><i class="ph-bold ph-plus text-slate-400 transition-transform duration-300"></i></span></button>
                        <div class="home-faq-content max-h-0 overflow-hidden transition-all duration-300 ease-in-out"><p class="px-6 pb-6 pt-1 text-sm leading-7 font-medium text-slate-500">{{ $faq[1] }}</p></div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-12">
        <div class="w-[97%] mx-auto reveal">
            <div class="relative overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-primary via-secondary to-primary p-8 sm:p-10 lg:p-14 text-center shadow-2xl">
                <div class="absolute -top-24 -right-20 h-64 w-64 rounded-full bg-white/15 blur-3xl"></div>
                <div class="absolute -bottom-24 -left-20 h-64 w-64 rounded-full bg-white/15 blur-3xl"></div>
                <div class="relative z-10 max-w-3xl mx-auto">
                    <p class="text-[11px] uppercase tracking-[0.26em] font-black text-blue-100">Start now</p>
                    <h2 class="mt-4 text-3xl sm:text-5xl font-black tracking-tight text-white">Bring real-time queue management to your clinic or find care faster as a patient.</h2>
                    <p class="mt-4 text-sm sm:text-base leading-8 font-medium text-blue-100">Create your patient account or submit a clinic registration request for owner approval.</p>
                    <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                        <a href="{{ route('register') }}" class="bg-white text-primary px-7 py-3.5 rounded-full text-sm sm:text-base font-black hover:bg-slate-50 transition-colors w-full sm:w-auto">Create Patient Account</a>
                        <a href="{{ route('clinic-applications.create') }}" class="border border-white/30 text-white px-7 py-3.5 rounded-full text-sm sm:text-base font-black hover:bg-white/10 transition-colors w-full sm:w-auto">Register Your Clinic</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        (() => {
            const revealObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;
                    entry.target.classList.add('active');
                    observer.unobserve(entry.target);
                });
            }, { threshold: 0.15, rootMargin: '0px 0px -50px 0px' });

            document.querySelectorAll('.reveal').forEach((el) => revealObserver.observe(el));
            setTimeout(() => document.querySelectorAll('.reveal').forEach((el) => {
                if (el.getBoundingClientRect().top < window.innerHeight) el.classList.add('active');
            }), 100);

            document.querySelectorAll('.home-faq-btn').forEach((button) => {
                button.addEventListener('click', () => {
                    const content = button.nextElementSibling;
                    const icon = button.querySelector('i');
                    const isOpen = Boolean(content.style.maxHeight);
                    document.querySelectorAll('.home-faq-content').forEach((item) => item.style.maxHeight = null);
                    document.querySelectorAll('.home-faq-btn i').forEach((itemIcon) => itemIcon.classList.remove('rotate-45', 'text-primary'));
                    if (!isOpen) {
                        content.style.maxHeight = `${content.scrollHeight}px`;
                        icon.classList.add('rotate-45', 'text-primary');
                    }
                });
            });

            const clinicCarousel = document.getElementById('premium-clinic-carousel');
            document.querySelectorAll('[data-clinic-scroll]').forEach((button) => {
                button.addEventListener('click', () => {
                    if (!clinicCarousel) return;

                    const direction = button.dataset.clinicScroll === 'left' ? -1 : 1;
                    clinicCarousel.scrollBy({
                        left: direction * 390,
                        behavior: 'smooth',
                    });
                });
            });
        })();
    </script>
@endsection
