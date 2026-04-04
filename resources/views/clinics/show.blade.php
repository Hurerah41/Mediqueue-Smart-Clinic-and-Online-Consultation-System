@extends('layouts.app', ['title' => $clinic->name.' | MediQueue'])

@section('content')
    @php
        $primaryColor = $clinic->brand_primary_color ?? '#2563EB';
        $secondaryColor = $clinic->brand_secondary_color ?? '#7C3AED';
        $clinicInitials = collect(explode(' ', $clinic->name))
            ->filter()
            ->take(2)
            ->map(fn ($word) => strtoupper(substr($word, 0, 1)))
            ->implode('');
        $weekdayLabels = [
            0 => 'Sun',
            1 => 'Mon',
            2 => 'Tue',
            3 => 'Wed',
            4 => 'Thu',
            5 => 'Fri',
            6 => 'Sat',
        ];
    @endphp

    <div class="glass-panel rounded-[2rem] p-8 shadow-glass mb-8">
        <div class="flex flex-wrap items-start justify-between gap-6">
            <div class="flex flex-wrap items-start gap-5">
                @if ($clinic->logo_path)
                    <img src="{{ asset('storage/'.$clinic->logo_path) }}" class="w-20 h-20 rounded-[1.75rem] object-cover border border-white shadow-soft" alt="{{ $clinic->name }} logo">
                @else
                    <div class="w-20 h-20 rounded-[1.75rem] text-white flex items-center justify-center text-3xl font-black shadow-glow" style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});">
                        {{ $clinicInitials ?: 'MC' }}
                    </div>
                @endif

                <div>
                <p class="text-sm uppercase tracking-widest text-primary font-bold">{{ $clinic->area->name }}</p>
                <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight text-dark mt-2">{{ $clinic->name }}</h1>
                @if ($clinic->brand_tagline)
                    <p class="text-base text-slate-600 mt-3 max-w-xl">{{ $clinic->brand_tagline }}</p>
                @endif
                <p class="text-slate-600 mt-3 flex items-center gap-2">
                    <i class="ph-fill ph-map-pin text-primary"></i>
                    {{ $clinic->address }}
                </p>
                <p class="text-slate-500 mt-2 flex items-center gap-2">
                    <i class="ph-fill ph-phone text-secondary"></i>
                    {{ $clinic->phone ?? 'Not available' }}
                </p>
                </div>
            </div>
            <div class="bg-white/80 rounded-3xl border border-white px-6 py-4 shadow-soft text-right" style="box-shadow: 0 18px 40px -20px {{ $primaryColor }}80;">
                <p class="text-xs uppercase tracking-widest text-slate-400 font-bold">Clinic Hours</p>
                <p class="text-xl font-black mt-2" style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }}); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    {{ $clinic->opens_at ? \Carbon\Carbon::parse($clinic->opens_at)->format('g:i A') : 'N/A' }}
                    -
                    {{ $clinic->closes_at ? \Carbon\Carbon::parse($clinic->closes_at)->format('g:i A') : 'N/A' }}
                </p>
                <p class="text-xs text-accent font-bold uppercase mt-2">Verified Clinic</p>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        @forelse ($clinic->doctors as $doctor)
            @php
                $rating = round((float) ($doctor->average_rating ?? 0), 1);
                $myReview = $patientReviews->get($doctor->id);
                $doctorInitials = collect(explode(' ', $doctor->user->name))
                    ->filter()
                    ->take(2)
                    ->map(fn ($word) => strtoupper(substr($word, 0, 1)))
                    ->implode('');
                $scheduleGroups = $doctor->schedules->groupBy('weekday');
                $ratingRows = collect([5, 4, 3, 2, 1])->mapWithKeys(fn ($score) => [
                    $score => $doctor->reviews->where('rating', $score)->count(),
                ]);
                $reviewTotal = max(1, $doctor->reviews->count());
            @endphp

            <div class="premium-card bg-white rounded-[2rem] border border-slate-100 p-6 md:p-8 shadow-soft">
                <div class="flex flex-col md:flex-row md:justify-between gap-4">
                    <div class="flex gap-4 items-start">
                        @if ($doctor->profile_photo_path)
                            <img src="{{ asset('storage/'.$doctor->profile_photo_path) }}" class="w-16 h-16 rounded-3xl object-cover border border-slate-100 shadow-soft" alt="Dr. {{ $doctor->user->name }}">
                        @else
                            <div class="w-16 h-16 rounded-3xl bg-gradient-premium text-white flex items-center justify-center text-xl font-black shadow-glow">
                                {{ $doctorInitials ?: 'DR' }}
                            </div>
                        @endif
                        <div>
                            <h2 class="text-2xl font-extrabold text-dark">Dr. {{ $doctor->user->name }}</h2>
                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                <span class="px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-wide">{{ $doctor->specialization->name }}</span>
                                <span class="px-3 py-1 rounded-full bg-secondary/10 text-secondary text-xs font-bold uppercase tracking-wide">{{ $doctor->experience_years }}+ Years</span>
                                <span class="px-3 py-1 rounded-full {{ $doctor->offers_online_consultation ? 'bg-green-50 text-green-600' : 'bg-slate-100 text-slate-500' }} text-xs font-bold uppercase tracking-wide">
                                    {{ $doctor->offers_online_consultation ? 'Online + Clinic' : 'Clinic Visit Only' }}
                                </span>
                            </div>
                            <p class="text-slate-600 mt-3">{{ $doctor->bio ?: 'Experienced specialist available for consultation.' }}</p>
                        </div>
                    </div>
                    <div class="md:text-right flex md:block items-center justify-between">
                        <div>
                            <div class="text-2xl font-black text-dark">Rs {{ number_format($doctor->consultation_fee) }}</div>
                            <div class="text-sm text-slate-500">Consultation fee</div>
                        </div>
                        <div class="md:mt-4 inline-flex items-center gap-1 px-3 py-2 rounded-2xl bg-amber-50 text-amber-500 font-black text-sm">
                            <i class="ph-fill ph-star"></i>
                            {{ $rating ?: 'New' }}
                            <span class="text-slate-400 font-semibold">({{ $doctor->reviews_count }})</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 grid md:grid-cols-2 gap-4">
                    <div class="rounded-3xl bg-slate-50 border border-slate-100 p-4">
                        <p class="text-xs uppercase tracking-widest text-slate-400 font-bold mb-3">Schedule Tabs</p>
                        <div class="flex flex-wrap gap-2">
                            @forelse ($scheduleGroups as $weekday => $slots)
                                <button type="button" data-doctor-tab="{{ $doctor->id }}-{{ $weekday }}" class="doctor-schedule-tab px-3 py-2 rounded-2xl border text-xs font-black transition-all {{ $loop->first ? 'bg-gradient-premium text-white border-transparent' : 'bg-white border-slate-100 text-dark hover:border-primary/30' }}">
                                    {{ $weekdayLabels[$weekday] ?? 'Day' }}
                                </button>
                            @empty
                                <span class="text-sm text-slate-500">Schedule not published yet.</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded-3xl bg-slate-50 border border-slate-100 p-4">
                        <p class="text-xs uppercase tracking-widest text-slate-400 font-bold mb-3">Consultation Timings</p>
                        <div>
                            @forelse ($scheduleGroups as $weekday => $slots)
                                <div data-doctor-panel="{{ $doctor->id }}-{{ $weekday }}" class="doctor-schedule-panel {{ $loop->first ? '' : 'hidden' }}">
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="w-10 h-10 rounded-2xl bg-primary/10 text-primary flex items-center justify-center font-black text-xs">{{ $weekdayLabels[$weekday] ?? 'Day' }}</span>
                                        <div class="text-sm font-bold text-dark">Available Slots</div>
                                    </div>
                                    <div class="space-y-2">
                                        @foreach ($slots as $slot)
                                            <div class="flex items-center justify-between gap-3 rounded-2xl bg-white px-4 py-3 border border-slate-100">
                                                <span class="text-sm font-bold text-dark">{{ $slot->starts_at->format('g:i A') }} - {{ $slot->ends_at->format('g:i A') }}</span>
                                                <span class="text-xs font-bold text-accent">{{ $slot->slot_limit }} slots</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500">No active slots configured.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                @auth
                    @if (auth()->user()->isPatient())
                        <form method="POST" action="{{ route('appointments.store') }}" class="grid md:grid-cols-2 gap-4 mt-6 border-t border-slate-100 pt-6">
                            @csrf
                            <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                            <input type="date" name="appointment_date" value="{{ now()->toDateString() }}" min="{{ now()->toDateString() }}" class="form-control" required>
                            <input type="time" name="appointment_time" class="form-control">
                            <select name="consultation_type" class="form-control">
                                <option value="physical">Physical Visit</option>
                                @if ($doctor->offers_online_consultation)
                                    <option value="online">Online Consultation</option>
                                @endif
                            </select>
                            <textarea name="symptoms" rows="2" class="md:col-span-2 form-control" placeholder="Describe symptoms"></textarea>
                            <button type="submit" class="md:col-span-2 bg-gradient-premium text-white py-3 rounded-2xl font-bold shadow-glow">Book Appointment</button>
                        </form>

                        <div class="mt-6 rounded-3xl border border-slate-100 bg-slate-50/80 p-5">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-xs uppercase tracking-widest text-secondary font-bold">Doctor Reviews</p>
                                    <h3 class="text-lg font-black text-dark mt-1">Patient feedback</h3>
                                </div>
                            </div>

                            <div class="mt-4 space-y-2">
                                @foreach ($ratingRows as $score => $count)
                                    <div class="flex items-center gap-3 text-xs font-bold text-slate-500">
                                        <span class="w-8">{{ $score }}★</span>
                                        <div class="flex-1 h-2 rounded-full bg-slate-200 overflow-hidden">
                                            <div class="h-full rounded-full bg-gradient-premium" style="width: {{ ($count / $reviewTotal) * 100 }}%;"></div>
                                        </div>
                                        <span class="w-6 text-right">{{ $count }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4 space-y-3">
                                @forelse ($doctor->reviews->take(3) as $review)
                                    <div class="bg-white rounded-2xl p-4 border border-slate-100">
                                        <div class="flex items-center justify-between gap-3">
                                            <p class="font-bold text-dark text-sm">{{ $review->user->name }}</p>
                                            <span class="text-amber-500 text-sm font-black">
                                                <i class="ph-fill ph-star"></i> {{ $review->rating }}/5
                                            </span>
                                        </div>
                                        <p class="text-sm text-slate-600 mt-2">{{ $review->comment }}</p>
                                    </div>
                                @empty
                                    <p class="text-sm text-slate-500">No reviews yet. Be the first patient to review this doctor.</p>
                                @endforelse
                            </div>

                            @if (in_array($doctor->id, $reviewableDoctorIds, true))
                                <form method="POST" action="{{ route('doctors.reviews.store', $doctor) }}" class="grid sm:grid-cols-[140px_minmax(0,1fr)] gap-3 mt-5">
                                    @csrf
                                    <select name="rating" class="form-control" required>
                                        @foreach ([5, 4, 3, 2, 1] as $score)
                                            <option value="{{ $score }}" @selected((int) ($myReview?->rating ?? 5) === $score)>{{ $score }} Stars</option>
                                        @endforeach
                                    </select>
                                    <input
                                        type="text"
                                        name="comment"
                                        value="{{ old('comment', $myReview?->comment) }}"
                                        class="form-control"
                                        placeholder="Write your doctor review"
                                        required
                                    >
                                    <button type="submit" class="sm:col-span-2 border border-secondary/20 text-secondary rounded-2xl py-3 font-bold hover:bg-secondary hover:text-white transition-all">
                                        {{ $myReview ? 'Update Review' : 'Submit Review' }}
                                    </button>
                                </form>
                            @else
                                <p class="text-xs text-slate-500 mt-4">You can review this doctor after booking an appointment.</p>
                            @endif
                        </div>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="inline-flex mt-6 text-primary font-semibold items-center gap-2">Login to book appointment <i class="ph-bold ph-arrow-right"></i></a>
                @endauth
            </div>
        @empty
            <div class="lg:col-span-2 bg-white rounded-[2rem] border border-slate-100 p-10 shadow-soft text-center">
                <div class="w-16 h-16 mx-auto rounded-3xl bg-slate-50 text-primary flex items-center justify-center text-3xl">
                    <i class="ph-fill ph-user-focus"></i>
                </div>
                <h2 class="text-2xl font-extrabold text-dark mt-5">No doctors listed yet</h2>
                <p class="text-slate-500 mt-2">This clinic is verified, but their doctors and schedules have not been published yet.</p>
            </div>
        @endforelse
    </div>

    <script>
        document.querySelectorAll('.doctor-schedule-tab').forEach((tab) => {
            tab.addEventListener('click', () => {
                const doctorId = tab.dataset.doctorTab.split('-')[0];

                document.querySelectorAll(`[data-doctor-tab^="${doctorId}-"]`).forEach((item) => {
                    item.className = 'doctor-schedule-tab px-3 py-2 rounded-2xl border text-xs font-black transition-all bg-white border-slate-100 text-dark hover:border-primary/30';
                });

                document.querySelectorAll(`[data-doctor-panel^="${doctorId}-"]`).forEach((panel) => {
                    panel.classList.add('hidden');
                });

                tab.className = 'doctor-schedule-tab px-3 py-2 rounded-2xl border text-xs font-black transition-all bg-gradient-premium text-white border-transparent';
                document.querySelector(`[data-doctor-panel="${tab.dataset.doctorTab}"]`)?.classList.remove('hidden');
            });
        });
    </script>
@endsection
