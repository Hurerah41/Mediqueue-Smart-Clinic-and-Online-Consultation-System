@extends('layouts.app', ['title' => 'MediQueue | Smart Healthcare & Queue Management'])

@section('content')
    <section id="home" class="relative pt-6 pb-16 overflow-hidden">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            <div class="relative z-10 flex flex-col items-start gap-8">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-primary/10 border border-primary/20 text-primary text-sm font-medium">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                    </span>
                    Now available across Karachi clinics
                </div>

                <h1 class="text-5xl lg:text-7xl font-extrabold tracking-tight leading-[1.1]">
                    Skip the waiting room.
                    <span class="text-gradient">See a doctor faster.</span>
                </h1>

                <p class="text-lg lg:text-xl text-slate-600 max-w-lg leading-relaxed">
                    Find nearby clinics, book a live queue token, consult doctors online, and download digital prescriptions from one SaaS platform.
                </p>

                <form action="{{ route('clinics.index') }}" method="GET" class="w-full max-w-md bg-white p-2 rounded-2xl shadow-soft border border-slate-100 flex items-center gap-2">
                    <div class="flex-1 flex items-center gap-3 px-4">
                        <i class="ph ph-map-pin text-slate-400 text-xl shrink-0"></i>
                        <select name="area_id" class="w-full bg-transparent outline-none text-dark py-2 appearance-none cursor-pointer">
                            <option value="">Search clinics by area...</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area->id }}">{{ $area->name }}, {{ $area->city }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="bg-primary text-white p-3 rounded-xl hover:bg-blue-700 transition-colors shadow-glow shrink-0" type="submit">
                        <i class="ph-bold ph-arrow-right"></i>
                    </button>
                </form>

                <div class="flex items-center gap-4">
                    <div class="flex -space-x-3">
                        <img src="https://i.pravatar.cc/100?img=1" class="w-10 h-10 rounded-full border-2 border-white shadow-sm" alt="User">
                        <img src="https://i.pravatar.cc/100?img=2" class="w-10 h-10 rounded-full border-2 border-white shadow-sm" alt="User">
                        <img src="https://i.pravatar.cc/100?img=3" class="w-10 h-10 rounded-full border-2 border-white shadow-sm" alt="User">
                        <div class="w-10 h-10 rounded-full border-2 border-white bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-600 shadow-sm">+2k</div>
                    </div>
                    <div class="text-sm text-slate-500 font-medium">
                        Trusted by <span class="text-dark font-bold">10,000+</span> patients
                    </div>
                </div>
            </div>

            <div class="relative z-10 lg:h-[600px] flex items-center justify-center animate-float">
                <div class="glass-panel w-full max-w-sm rounded-[2rem] shadow-2xl border border-white p-6 relative">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <p class="text-sm text-slate-500 font-medium">Current Status</p>
                            <h3 class="text-xl font-bold text-dark">Live Queue</h3>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-primary text-xl">
                            <i class="ph-fill ph-ticket"></i>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 mb-4 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-50 to-transparent rounded-bl-full z-0"></div>
                        <div class="relative z-10">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <p class="text-xs font-semibold text-primary uppercase tracking-wider">Your Token</p>
                                    <p class="text-4xl font-black text-dark tracking-tighter mt-1">#A-42</p>
                                </div>
                                <span class="px-2.5 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-md">Confirmed</span>
                            </div>

                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-500">Estimated Time</span>
                                    <span class="font-semibold text-dark">14 mins</span>
                                </div>
                                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                    <div class="bg-primary h-full rounded-full w-[70%] relative">
                                        <div class="absolute right-0 top-0 bottom-0 w-4 bg-white/30 animate-pulse"></div>
                                    </div>
                                </div>
                                <div class="flex justify-between text-xs text-slate-400">
                                    <span>Currently serving: #A-38</span>
                                    <span>3 people ahead</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 flex items-center gap-4">
                        <div class="relative">
                            <img src="https://i.pravatar.cc/150?img=32" class="w-12 h-12 rounded-full object-cover" alt="Doctor">
                            <div class="absolute bottom-0 right-0 w-3 h-3 bg-accent border-2 border-white rounded-full"></div>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-dark text-sm">Dr. Ayesha Khan</h4>
                            <p class="text-xs text-slate-500">General Physician</p>
                        </div>
                        <button class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-colors" type="button">
                            <i class="ph-bold ph-video-camera"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="border-y border-slate-200/60 bg-white/50 backdrop-blur-sm rounded-[2rem]">
        <div class="grid grid-cols-2 md:grid-cols-4 text-center">
            @foreach ([['2500+', 'Partner Clinics'], ['15000+', 'Verified Doctors'], ['2.5M+', 'Patients Served'], ['98%', 'Satisfaction Rate']] as [$value, $label])
                <div class="flex flex-col items-center justify-center p-6 border-slate-200/60 border-r last:border-r-0">
                    <p class="text-4xl font-extrabold text-dark tracking-tight mb-2">{{ $value }}</p>
                    <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section id="how-it-works" class="py-20 relative z-10">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <h2 class="text-sm font-bold text-primary uppercase tracking-widest mb-3">Simple Process</h2>
            <h3 class="text-4xl font-extrabold text-dark tracking-tight">Your health, streamlined in three steps.</h3>
        </div>

        <div class="grid md:grid-cols-3 gap-8 relative">
            @foreach ([
                ['ph-fill ph-map-pin-line', 'Find Nearby Clinic', 'Locate clinics and specialists in your area based on live availability.', 'bg-blue-50 text-primary', '1'],
                ['ph-fill ph-ticket', 'Book Your Token', 'Reserve your queue token and track your turn from your phone.', 'bg-purple-50 text-secondary', '2'],
                ['ph-fill ph-stethoscope', 'Visit or Consult', 'Walk in on time or start a video consultation from home.', 'bg-green-50 text-accent', '3'],
            ] as [$icon, $titleText, $copy, $iconClass, $step])
                <div class="bg-white rounded-3xl p-8 shadow-soft border border-slate-100 relative hover:-translate-y-2 hover:shadow-xl transition-all duration-300 group">
                    <div class="w-14 h-14 rounded-2xl {{ $iconClass }} flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-all duration-300">
                        <i class="{{ $icon }}"></i>
                    </div>
                    <span class="absolute top-8 right-8 text-6xl font-black text-slate-50 opacity-70 select-none">{{ $step }}</span>
                    <h4 class="text-xl font-bold text-dark mb-3">{{ $titleText }}</h4>
                    <p class="text-slate-500 leading-relaxed">{{ $copy }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section id="features" class="py-20 bg-slate-900 relative overflow-hidden rounded-[2.5rem] px-8">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMSIgZmlsbD0icmdiYSgyNTUsMjU1LDI1NSwwLjA1KSIvPjwvc3ZnPg==')] opacity-50"></div>
        <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-primary/20 rounded-full mix-blend-screen filter blur-[120px] opacity-40 pointer-events-none"></div>

        <div class="relative z-10">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-sm font-bold text-blue-400 uppercase tracking-widest mb-3">Powerful Features</h2>
                <h3 class="text-4xl font-extrabold text-white tracking-tight">Everything you need for seamless healthcare.</h3>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ([
                    ['ph-fill ph-clock-countdown', 'Live Queue Management', 'Monitor queue position in real time with estimated waiting time.', 'bg-blue-500/20 text-blue-400'],
                    ['ph-fill ph-video-camera', 'HD Video Consultations', 'Connect remotely through secure video and chat consultations.', 'bg-purple-500/20 text-purple-400'],
                    ['ph-fill ph-folder-notch-open', 'Digital Prescriptions', 'Access prescriptions and consultation history from your dashboard.', 'bg-green-500/20 text-green-400'],
                ] as [$icon, $titleText, $copy, $iconClass])
                    <div class="gradient-border rounded-3xl transition-transform duration-300 hover:-translate-y-1">
                        <div class="bg-slate-800/80 backdrop-blur-sm rounded-[calc(1.5rem-1px)] p-8 h-full">
                            <div class="w-12 h-12 rounded-xl {{ $iconClass }} flex items-center justify-center text-xl mb-6">
                                <i class="{{ $icon }}"></i>
                            </div>
                            <h4 class="text-lg font-bold text-white mb-2">{{ $titleText }}</h4>
                            <p class="text-slate-400 text-sm leading-relaxed">{{ $copy }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="clinics" class="py-20">
        <div class="flex justify-between items-end mb-10">
            <div>
                <h2 class="text-3xl font-extrabold text-dark tracking-tight">Top-rated Clinics Near You</h2>
                <p class="text-slate-500 mt-2">Book queues instantly at verified healthcare centers.</p>
            </div>
            <a href="{{ route('clinics.index') }}" class="hidden md:inline-flex text-primary font-semibold items-center gap-2">
                View all <i class="ph-bold ph-arrow-right"></i>
            </a>
        </div>

        <div class="flex overflow-x-auto snap-x snap-mandatory hide-scrollbar gap-6 pb-6">
            @foreach ($clinics as $clinic)
                @php
                    $primaryColor = $clinic->brand_primary_color ?? '#2563EB';
                    $secondaryColor = $clinic->brand_secondary_color ?? '#7C3AED';
                    $clinicInitials = collect(explode(' ', $clinic->name))
                        ->filter()
                        ->take(2)
                        ->map(fn ($word) => strtoupper(substr($word, 0, 1)))
                        ->implode('');
                @endphp

                <a href="{{ route('clinics.show', $clinic) }}" class="premium-card snap-start shrink-0 w-[85vw] sm:w-80 bg-white rounded-3xl shadow-soft border border-slate-100 p-5 group">
                    <div class="h-40 rounded-2xl mb-4 overflow-hidden relative p-5 flex items-end" style="background: linear-gradient(135deg, {{ $primaryColor }}20, {{ $secondaryColor }}35);">
                        <div class="absolute inset-0 opacity-60" style="background: radial-gradient(circle at top right, {{ $primaryColor }}33, transparent 40%), radial-gradient(circle at bottom left, {{ $secondaryColor }}33, transparent 45%);"></div>
                        <div class="relative z-10 flex items-center gap-4">
                            @if ($clinic->logo_path)
                                <img src="{{ asset('storage/'.$clinic->logo_path) }}" class="w-16 h-16 rounded-3xl object-cover bg-white border border-white/80 shadow-soft" alt="{{ $clinic->name }} logo">
                            @else
                                <div class="w-16 h-16 rounded-3xl text-white flex items-center justify-center text-xl font-black shadow-glow" style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});">
                                    {{ $clinicInitials ?: 'MC' }}
                                </div>
                            @endif
                            <div>
                                <p class="text-xs uppercase tracking-widest font-black" style="color: {{ $primaryColor }};">{{ $clinic->area->name }}</p>
                                <p class="text-lg font-black text-dark leading-tight">{{ $clinic->name }}</p>
                            </div>
                        </div>
                        <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-2 py-1 rounded-lg text-xs font-bold text-dark flex items-center gap-1 shadow-sm">
                            <i class="ph-fill ph-star text-amber-400"></i> {{ number_format($clinic->doctors->avg(fn ($doctor) => $doctor->reviews->avg('rating') ?? 4.9) ?: 4.9, 1) }}
                        </div>
                    </div>
                    <h4 class="text-lg font-bold text-dark mb-1">{{ $clinic->name }}</h4>
                    @if ($clinic->brand_tagline)
                        <p class="text-sm text-slate-600 mb-3">{{ $clinic->brand_tagline }}</p>
                    @endif
                    <p class="text-sm text-slate-500 mb-4 flex items-center gap-1">
                        <i class="ph-fill ph-map-pin text-slate-400"></i> {{ $clinic->area->name }}, {{ $clinic->address }}
                    </p>
                    <div class="flex flex-wrap items-center gap-2 mb-5">
                        @foreach ($clinic->doctors->take(2) as $doctor)
                            <span class="text-xs font-medium px-2 py-1 bg-slate-100 text-slate-600 rounded-md">{{ $doctor->specialization->name }}</span>
                        @endforeach
                    </div>
                    <span class="block w-full text-center py-2.5 rounded-xl bg-primary/10 text-primary font-semibold text-sm group-hover:bg-primary group-hover:text-white transition-colors">
                        Book Queue
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    <section class="py-12">
        <div class="bg-gradient-premium rounded-[3rem] p-12 md:p-20 text-center relative overflow-hidden shadow-2xl">
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl"></div>
            <div class="relative z-10 max-w-2xl mx-auto">
                <h2 class="text-4xl md:text-5xl font-extrabold text-white mb-6 tracking-tight">Ready to upgrade your healthcare experience?</h2>
                <p class="text-blue-100 text-lg mb-10">Create your free account and start booking queue tokens in seconds.</p>
                <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                    <a href="{{ route('clinics.index') }}" class="bg-white text-primary px-8 py-4 rounded-full font-bold text-lg hover:bg-slate-50 hover:scale-105 transition-all duration-300 shadow-xl w-full sm:w-auto">Find a Clinic Now</a>
                    <a href="{{ route('register') }}" class="bg-transparent border border-white/30 text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-white/10 transition-all duration-300 w-full sm:w-auto">Create Patient Account</a>
                </div>
            </div>
        </div>
    </section>
@endsection
