@extends('layouts.app', ['title' => 'Clinics | MediQueue'])

@section('content')
    <section class="relative overflow-hidden pb-8">
        <!-- <div class="orb-backdrop"></div> -->

        <div class="w-[97%] mx-auto">
            <div class="max-w-3xl mx-auto text-center reveal">
                <div class="hero-badge inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold text-slate-500 mb-6">
                    <span class="w-2 h-2 rounded-full bg-accent animate-pulse-soft"></span>
                    Verified clinics
                </div>

                <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight leading-[1.06] text-gradient">
                    Find the right clinic
                    <br class="hidden sm:block">
                    without the guesswork
                </h1>

                <p class="max-w-2xl mx-auto mt-5 text-base sm:text-lg leading-relaxed text-slate-500 font-medium">
                    Search approved clinics by area or specialty, then open the clinic profile to see doctors, timings, fees, reviews, and online consultation options.
                </p>
            </div>

            <div class="mt-8 reveal reveal-delay-1">
                <form method="GET" action="{{ route('clinics.index') }}" class="bento-card rounded-[2rem] p-2.5 flex flex-col xl:flex-row gap-2">
                    <div class="flex-1 surface-white rounded-[1.35rem] px-4 py-3 flex items-center gap-3">
                        <i class="ph ph-magnifying-glass text-xl text-slate-400"></i>
                        <input
                            type="text"
                            name="search"
                            value="{{ $searchTerm }}"
                            placeholder="Search clinic, doctor, or specialization"
                            class="w-full bg-transparent text-sm font-semibold text-dark outline-none"
                        >
                    </div>

                    <div class="xl:w-60 surface-white rounded-[1.35rem] px-4 py-3 flex items-center gap-3">
                        <i class="ph ph-map-pin text-lg text-slate-400"></i>
                        <select name="area_id" class="w-full bg-transparent text-sm font-semibold text-dark outline-none appearance-none cursor-pointer">
                            <option value="">All areas</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area->id }}" @selected($selectedAreaId === $area->id)>{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="xl:w-64 surface-white rounded-[1.35rem] px-4 py-3 flex items-center gap-3">
                        <i class="ph ph-stethoscope text-lg text-slate-400"></i>
                        <select name="specialization_id" class="w-full bg-transparent text-sm font-semibold text-dark outline-none appearance-none cursor-pointer">
                            <option value="">All specializations</option>
                            @foreach ($specializations as $specialization)
                                <option value="{{ $specialization->id }}" @selected($selectedSpecializationId === $specialization->id)>{{ $specialization->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="xl:w-auto bg-dark text-white rounded-[1.35rem] px-7 py-4 text-sm font-bold hover:bg-primary transition-colors shadow-sm">
                        Find Clinics
                    </button>
                </form>
            </div>
        </div>
    </section>

    <section class="w-[97%] mx-auto pb-14">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3 reveal reveal-delay-1">
            @forelse ($clinics as $clinic)
                @php
                    $primaryColor = $clinic->brand_primary_color ?? '#2563EB';
                    $secondaryColor = $clinic->brand_secondary_color ?? '#7C3AED';
                    $clinicInitials = collect(explode(' ', $clinic->name))
                        ->filter()
                        ->take(2)
                        ->map(fn ($word) => strtoupper(substr($word, 0, 1)))
                        ->implode('');
                    $rating = $clinic->doctors->avg(fn ($doctor) => $doctor->reviews->avg('rating') ?? 4.8) ?: 4.8;
                    $isOpen = $clinic->isOpenAt(now());
                    $specialtyTags = $clinic->doctors
                        ->pluck('specialization.name')
                        ->filter()
                        ->unique()
                        ->take(3)
                        ->values();
                    $doctorCount = $clinic->doctors_count ?? $clinic->doctors->count();
                    $doctorLabel = $doctorCount === 1 ? '1 doctor available' : $doctorCount.' doctors available';
                @endphp

                <div class="group rounded-[2.5rem] p-3 bg-gradient-to-br from-white/85 to-white/55 backdrop-blur-2xl border border-white/60 shadow-[0_28px_70px_-45px_rgba(15,23,42,0.42),inset_0_1px_0_rgba(255,255,255,0.92),inset_0_0_0_1px_rgba(255,255,255,0.45)] transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] hover:-translate-y-2 hover:shadow-[0_38px_90px_-42px_rgba(37,99,235,0.45),inset_0_1px_0_rgba(255,255,255,1)]">
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
                                    {{ number_format($rating, 1) }}
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
                        <h2 class="text-2xl font-black tracking-tight text-dark transition-colors duration-300 group-hover:text-primary">{{ $clinic->name }}</h2>
                        <p class="mt-2 flex items-center gap-2 text-sm font-semibold text-slate-500">
                            <i class="ph-fill ph-map-pin text-slate-400"></i>
                            {{ \Illuminate\Support\Str::limit($clinic->address, 58) }}
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

                            <a href="{{ route('clinics.show', $clinic) }}" class="shine-button mt-4 block rounded-full bg-dark px-5 py-3 text-center text-xs font-black text-white transition-all duration-300 hover:bg-primary">
                                View Clinic
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bento-card rounded-[2rem] p-10 text-center">
                    <h2 class="text-2xl font-extrabold text-dark">No clinics matched your filters</h2>
                    <p class="mt-3 text-sm text-slate-500 font-medium">Try another area, specialization, or search term to see more clinics.</p>
                </div>
            @endforelse
        </div>

        @if ($clinics->hasPages())
            <div class="mt-8 reveal reveal-delay-2">
                {{ $clinics->links() }}
            </div>
        @endif
    </section>

    <script>
        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('active');
                observer.unobserve(entry.target);
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -50px 0px' });

        document.querySelectorAll('.reveal').forEach((element) => revealObserver.observe(element));

        setTimeout(() => {
            document.querySelectorAll('.reveal').forEach((element) => {
                if (element.getBoundingClientRect().top < window.innerHeight) {
                    element.classList.add('active');
                }
            });
        }, 100);
    </script>
@endsection
