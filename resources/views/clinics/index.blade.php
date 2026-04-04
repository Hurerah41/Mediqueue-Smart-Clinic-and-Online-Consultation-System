@extends('layouts.app', ['title' => 'Clinics | MediQueue'])

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4 mb-10">
        <div>
            <p class="text-sm uppercase tracking-widest text-primary font-bold">Clinics</p>
            <h1 class="text-4xl md:text-5xl font-extrabold text-dark tracking-tight mt-2">Browse clinics by area, name, or specialty</h1>
        </div>

        <form method="GET" action="{{ route('clinics.index') }}" class="w-full xl:w-auto grid md:grid-cols-[minmax(220px,1fr)_200px_220px_auto] gap-3 bg-white rounded-[1.5rem] shadow-soft border border-slate-100 p-3">
            <div class="flex items-center gap-3 px-3 py-2 rounded-2xl bg-slate-50">
                <i class="ph ph-magnifying-glass text-slate-400 text-xl"></i>
                <input
                    type="text"
                    name="search"
                    value="{{ $searchTerm }}"
                    placeholder="Search clinic, doctor, or specialization"
                    class="w-full bg-transparent outline-none text-sm text-slate-700"
                >
            </div>
            <select name="area_id" class="form-control">
                <option value="">All areas</option>
                @foreach ($areas as $area)
                    <option value="{{ $area->id }}" @selected($selectedAreaId === $area->id)>{{ $area->name }}</option>
                @endforeach
            </select>
            <select name="specialization_id" class="form-control">
                <option value="">All specializations</option>
                @foreach ($specializations as $specialization)
                    <option value="{{ $specialization->id }}" @selected($selectedSpecializationId === $specialization->id)>{{ $specialization->name }}</option>
                @endforeach
            </select>
            <button class="bg-gradient-premium text-white px-8 py-3 rounded-2xl font-bold shadow-glow hover:-translate-y-0.5 transition-all" type="submit">Search</button>
        </form>
    </div>

    <div class="grid md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
        @forelse ($clinics as $clinic)
            @php
                $primaryColor = $clinic->brand_primary_color ?? '#2563EB';
                $secondaryColor = $clinic->brand_secondary_color ?? '#7C3AED';
                $clinicInitials = collect(explode(' ', $clinic->name))
                    ->filter()
                    ->take(2)
                    ->map(fn ($word) => strtoupper(substr($word, 0, 1)))
                    ->implode('');
            @endphp

            <a href="{{ route('clinics.show', $clinic) }}" class="premium-card bg-white rounded-3xl border border-slate-100 p-5 shadow-soft group">
                <div class="h-40 rounded-2xl mb-4 overflow-hidden relative p-5 flex items-end" style="background: linear-gradient(135deg, {{ $primaryColor }}20, {{ $secondaryColor }}35);">
                    <div class="absolute inset-0 opacity-60 group-hover:scale-110 transition-transform duration-700" style="background: radial-gradient(circle at top right, {{ $primaryColor }}33, transparent 40%), radial-gradient(circle at bottom left, {{ $secondaryColor }}33, transparent 45%);"></div>
                    <div class="relative z-10 flex items-center gap-4 group-hover:scale-[1.02] transition-transform duration-500">
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
                        <i class="ph-fill ph-star text-amber-400"></i> {{ number_format($clinic->doctors->avg(fn ($doctor) => $doctor->reviews->avg('rating') ?? 4.8) ?: 4.8, 1) }}
                    </div>
                </div>
                <h2 class="text-xl font-bold text-dark mb-2">{{ $clinic->name }}</h2>
                @if ($clinic->brand_tagline)
                    <p class="text-sm text-slate-600 mb-3">{{ $clinic->brand_tagline }}</p>
                @endif
                <p class="text-sm text-slate-500 mb-4 flex items-center gap-1">
                    <i class="ph-fill ph-map-pin text-slate-400"></i> {{ $clinic->area->name }}, {{ $clinic->address }}
                </p>
                <div class="flex flex-wrap gap-2 mb-5">
                    @foreach ($clinic->doctors->take(2) as $doctor)
                        <span class="text-xs font-medium px-2 py-1 bg-slate-100 text-slate-600 rounded-md">{{ $doctor->specialization->name }}</span>
                    @endforeach
                </div>
                <span class="block w-full py-2.5 rounded-xl text-white font-semibold text-sm text-center shadow-glow" style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});">View Doctors & Book</span>
            </a>
        @empty
            <div class="col-span-3 bg-white rounded-3xl border border-slate-100 p-10 text-center text-slate-500 shadow-soft">
                No clinics found in this area.
            </div>
        @endforelse
    </div>
@endsection
