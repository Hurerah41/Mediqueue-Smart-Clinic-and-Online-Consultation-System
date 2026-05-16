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
        $onlineDoctors = $clinic->doctors->where('offers_online_consultation', true)->count();
        $totalReviews = $clinic->doctors->sum('reviews_count');
        $startingFee = $clinic->doctors->min('consultation_fee');
        $isClinicOpen = $clinic->isOpenAt(now());
    @endphp

    <section class="relative pb-8">
        <!-- <div class="orb-backdrop"></div> -->

        <div class="w-[97%] mx-auto">
            <div class="auth-shell rounded-[2.25rem] p-6 sm:p-8">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                    <div class="flex items-start gap-4 sm:gap-5">
                        @if ($clinic->logo_path)
                            <img src="{{ asset('storage/'.$clinic->logo_path) }}" class="w-16 h-16 sm:w-20 sm:h-20 rounded-[1.4rem] object-cover border border-white shadow-soft" alt="{{ $clinic->name }} logo">
                        @else
                            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-[1.4rem] text-white flex items-center justify-center text-2xl font-extrabold shadow-glow-primary" style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});">
                                {{ $clinicInitials ?: 'MC' }}
                            </div>
                        @endif

                        <div>
                            <p class="text-[11px] uppercase tracking-[0.24em] font-black" style="color: {{ $primaryColor }};">{{ $clinic->area->name }}</p>
                            <h1 class="mt-2 text-3xl sm:text-5xl font-extrabold tracking-tight text-dark leading-[1.03]">{{ $clinic->name }}</h1>
                            <p class="mt-4 max-w-3xl text-sm sm:text-base leading-8 text-slate-600 font-medium">
                                {{ $clinic->brand_tagline ?: 'Verified clinic profile with doctors, fees, timings, reviews, and booking support in one place.' }}
                            </p>
                            <div class="mt-5 flex flex-wrap gap-3 text-sm font-medium text-slate-500">
                                <span class="surface-white rounded-full px-4 py-2 flex items-center gap-2">
                                    <i class="ph-fill ph-map-pin text-slate-400"></i>
                                    {{ $clinic->address }}
                                </span>
                                <span class="surface-white rounded-full px-4 py-2 flex items-center gap-2">
                                    <i class="ph-fill ph-phone text-slate-400"></i>
                                    {{ $clinic->phone ?? 'Phone not available' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="surface-white rounded-[1.6rem] p-5 min-w-[240px]">
                        <p class="text-[11px] uppercase tracking-[0.22em] font-black text-slate-400">Clinic hours</p>
                        <p class="mt-3 text-xl font-extrabold text-dark">
                            {{ $clinic->hoursLabel() }}
                        </p>
                        <p class="mt-2 text-sm font-bold uppercase {{ $isClinicOpen ? 'text-accent' : 'text-rose-600' }}">
                            {{ $isClinicOpen ? 'Open now' : 'Closed now' }}
                        </p>
                        <p class="mt-1 text-xs text-slate-500 font-semibold">Tokens are available only during clinic hours.</p>
                    </div>
                </div>

                <div class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="surface-white rounded-[1.35rem] p-4">
                        <p class="text-[11px] uppercase tracking-[0.2em] font-black text-slate-400">Doctors</p>
                        <p class="mt-2 text-2xl font-extrabold text-dark">{{ $clinic->doctors->count() }}</p>
                        <p class="text-sm text-slate-500 font-medium">Published specialists at this clinic</p>
                    </div>
                    <div class="surface-white rounded-[1.35rem] p-4">
                        <p class="text-[11px] uppercase tracking-[0.2em] font-black text-slate-400">Online support</p>
                        <p class="mt-2 text-2xl font-extrabold text-dark">{{ $onlineDoctors }}</p>
                        <p class="text-sm text-slate-500 font-medium">Doctors offering online consultation</p>
                    </div>
                    <div class="surface-white rounded-[1.35rem] p-4">
                        <p class="text-[11px] uppercase tracking-[0.2em] font-black text-slate-400">Reviews</p>
                        <p class="mt-2 text-2xl font-extrabold text-dark">{{ $totalReviews }}</p>
                        <p class="text-sm text-slate-500 font-medium">Patient reviews across all listed doctors</p>
                    </div>
                    <div class="surface-white rounded-[1.35rem] p-4">
                        <p class="text-[11px] uppercase tracking-[0.2em] font-black text-slate-400">Fees from</p>
                        <p class="mt-2 text-2xl font-extrabold text-dark">Rs {{ number_format($startingFee ?? 0) }}</p>
                        <p class="text-sm text-slate-500 font-medium">Starting consultation fee shown below</p>
                    </div>
                </div>
            </div>

            <!-- <div class="grid gap-4 lg:grid-cols-[1.2fr_0.8fr] mt-4">
                <div class="bento-card rounded-[1.8rem] p-5 sm:p-6">
                    <p class="text-[11px] uppercase tracking-[0.22em] font-black text-slate-400">What patients can see here</p>
                    <div class="mt-4 grid sm:grid-cols-2 gap-3">
                        <div class="surface-white rounded-[1.2rem] p-4">
                            <p class="text-sm font-extrabold text-dark">Doctor list and specialization</p>
                            <p class="mt-2 text-sm text-slate-500 font-medium">Every listed doctor includes specialization, fee, experience, and clinic availability.</p>
                        </div>
                        <div class="surface-white rounded-[1.2rem] p-4">
                            <p class="text-sm font-extrabold text-dark">Timings and clinic days</p>
                            <p class="mt-2 text-sm text-slate-500 font-medium">Patients can switch between available days and view exact consultation time slots.</p>
                        </div>
                        <div class="surface-white rounded-[1.2rem] p-4">
                            <p class="text-sm font-extrabold text-dark">Online or physical mode</p>
                            <p class="mt-2 text-sm text-slate-500 font-medium">Consultation mode is visible before booking so patients know what to expect.</p>
                        </div>
                        <div class="surface-white rounded-[1.2rem] p-4">
                            <p class="text-sm font-extrabold text-dark">Reviews and booking</p>
                            <p class="mt-2 text-sm text-slate-500 font-medium">Patients can read feedback, choose a doctor, and book an appointment directly from this page.</p>
                        </div>
                    </div>
                </div>

                <div class="bento-card rounded-[1.8rem] p-5 sm:p-6">
                    <p class="text-[11px] uppercase tracking-[0.22em] font-black text-slate-400">Quick overview</p>
                    <div class="mt-4 space-y-3 text-sm font-semibold text-slate-600">
                        <div class="surface-white rounded-[1.1rem] px-4 py-3 flex items-center justify-between">
                            <span>Area</span>
                            <span class="text-dark font-extrabold">{{ $clinic->area->name }}</span>
                        </div>
                        <div class="surface-white rounded-[1.1rem] px-4 py-3 flex items-center justify-between">
                            <span>Booking support</span>
                            <span class="text-dark font-extrabold">Live queue enabled</span>
                        </div>
                        <div class="surface-white rounded-[1.1rem] px-4 py-3 flex items-center justify-between">
                            <span>Online capable</span>
                            <span class="text-dark font-extrabold">{{ $onlineDoctors > 0 ? 'Yes' : 'No' }}</span>
                        </div>
                        <div class="surface-white rounded-[1.1rem] px-4 py-3 flex items-center justify-between">
                            <span>Clinic status</span>
                            <span class="{{ $isClinicOpen ? 'text-accent' : 'text-rose-600' }} font-extrabold uppercase">{{ $isClinicOpen ? 'Open' : 'Closed' }}</span>
                        </div>
                    </div>
                </div>
            </div> -->
        <!-- </div>  -->
    </section>

    <section class="w-[97%] mx-auto pb-12">
        <div class="grid gap-5 lg:grid-cols-2">
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

                <div class="premium-card bento-card rounded-[1.9rem] p-5 sm:p-6">
                    <div class="flex flex-col lg:flex-row lg:justify-between gap-5">
                        <div class="flex gap-4 items-start">
                            @if ($doctor->profile_photo_path)
                                <img src="{{ asset('storage/'.$doctor->profile_photo_path) }}" class="w-16 h-16 rounded-[1.2rem] object-cover border border-slate-100 shadow-soft" alt="Dr. {{ $doctor->user->name }}">
                            @else
                                <div class="w-16 h-16 rounded-[1.2rem] bg-gradient-premium text-white flex items-center justify-center text-xl font-extrabold shadow-glow-primary">
                                    {{ $doctorInitials ?: 'DR' }}
                                </div>
                            @endif

                            <div>
                                <h2 class="text-2xl font-extrabold text-dark">Dr. {{ $doctor->user->name }}</h2>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <span class="rounded-full bg-primary/10 text-primary px-3 py-1 text-[11px] font-bold uppercase tracking-wide">{{ $doctor->specialization->name }}</span>
                                    <span class="rounded-full bg-secondary/10 text-secondary px-3 py-1 text-[11px] font-bold uppercase tracking-wide">{{ $doctor->experience_years }}+ years</span>
                                    <span class="rounded-full {{ $doctor->offers_online_consultation ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500' }} px-3 py-1 text-[11px] font-bold uppercase tracking-wide">
                                        {{ $doctor->offers_online_consultation ? 'Online + Clinic' : 'Clinic only' }}
                                    </span>
                                </div>
                                <p class="mt-4 text-sm leading-7 text-slate-600 font-medium">{{ $doctor->bio ?: 'Experienced specialist available for consultation at this clinic.' }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 lg:grid-cols-1 gap-3 lg:min-w-[160px]">
                            <div class="surface-white rounded-[1.3rem] p-4 text-center">
                                <p class="text-[11px] uppercase tracking-[0.2em] font-black text-slate-400">Consultation fee</p>
                                <p class="mt-2 text-xl font-extrabold text-dark">Rs {{ number_format($doctor->consultation_fee) }}</p>
                            </div>
                            <div class="surface-white rounded-[1.3rem] p-4 text-center">
                                <p class="text-[11px] uppercase tracking-[0.2em] font-black text-slate-400">Rating</p>
                                <p class="mt-2 text-xl font-extrabold text-dark">{{ $rating ?: 'New' }}</p>
                                <p class="text-xs text-slate-500 font-medium">{{ $doctor->reviews_count }} reviews</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 grid md:grid-cols-2 gap-4">
                        <div class="surface-white rounded-[1.5rem] p-4">
                            <p class="text-[11px] uppercase tracking-[0.22em] font-black text-slate-400">Available days</p>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @forelse ($scheduleGroups as $weekday => $slots)
                                    <button type="button" data-doctor-tab="{{ $doctor->id }}-{{ $weekday }}" class="doctor-schedule-tab px-3 py-2 rounded-full border text-xs font-bold transition-all {{ $loop->first ? 'bg-dark text-white border-dark' : 'bg-white border-slate-200 text-dark hover:border-primary/40' }}">
                                        {{ $weekdayLabels[$weekday] ?? 'Day' }}
                                    </button>
                                @empty
                                    <span class="text-sm text-slate-500">Schedule not published yet.</span>
                                @endforelse
                            </div>
                        </div>

                        <div class="surface-white rounded-[1.5rem] p-4">
                            <p class="text-[11px] uppercase tracking-[0.22em] font-black text-slate-400">Consultation timings</p>
                            <div class="mt-4">
                                @forelse ($scheduleGroups as $weekday => $slots)
                                    <div data-doctor-panel="{{ $doctor->id }}-{{ $weekday }}" class="doctor-schedule-panel {{ $loop->first ? '' : 'hidden' }}">
                                        <div class="space-y-2">
                                            @foreach ($slots as $slot)
                                                <div class="rounded-[1rem] bg-white border border-slate-100 px-4 py-3 flex items-center justify-between gap-3">
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
                            @if (! $isClinicOpen)
                                <div class="mt-5 rounded-[1.4rem] border border-rose-100 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700">
                                    This clinic is closed right now. You can book a token during clinic hours: {{ $clinic->hoursLabel() }}.
                                </div>
                            @endif

                            <form method="POST" action="{{ route('appointments.store') }}" class="grid md:grid-cols-2 gap-4 mt-5 border-t border-slate-100 pt-5">
                                @csrf
                                <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                                <input type="date" name="appointment_date" value="{{ now()->toDateString() }}" min="{{ now()->toDateString() }}" class="form-control" required @disabled(! $isClinicOpen)>
                                <input type="time" name="appointment_time" class="form-control" @disabled(! $isClinicOpen)>
                                <select name="consultation_type" class="form-control" @disabled(! $isClinicOpen)>
                                    <option value="physical">Physical Visit</option>
                                    @if ($doctor->offers_online_consultation)
                                        <option value="online">Online Consultation</option>
                                    @endif
                                </select>
                                @if ($doctor->offers_online_consultation)
                                    <div class="md:col-span-2 rounded-[1.2rem] bg-blue-50 border border-blue-100 px-4 py-3 text-sm font-semibold text-primary">
                                        Online consultations require payment first. Your queue token and video room are created after payment confirmation.
                                    </div>
                                @endif
                                <textarea name="symptoms" rows="2" class="md:col-span-2 form-control" placeholder="Describe symptoms" @disabled(! $isClinicOpen)></textarea>
                                <button type="submit" class="md:col-span-2 {{ $isClinicOpen ? 'bg-dark text-white hover:bg-primary' : 'bg-slate-200 text-slate-500 cursor-not-allowed' }} py-3.5 rounded-[1.2rem] font-bold transition-colors" @disabled(! $isClinicOpen)>
                                    {{ $isClinicOpen ? 'Book Appointment' : 'Clinic Closed' }}
                                </button>
                            </form>

                            <div class="mt-5 surface-white rounded-[1.5rem] p-5">
                                <p class="text-[11px] uppercase tracking-[0.22em] font-black text-secondary">Doctor reviews</p>
                                <h3 class="mt-2 text-lg font-extrabold text-dark">What patients are saying</h3>

                                <div class="mt-4 space-y-2">
                                    @foreach ($ratingRows as $score => $count)
                                        <div class="flex items-center gap-3 text-xs font-bold text-slate-500">
                                            <span class="w-8">{{ $score }}*</span>
                                            <div class="flex-1 h-2 rounded-full bg-slate-200 overflow-hidden">
                                                <div class="h-full rounded-full bg-gradient-premium" style="width: {{ ($count / $reviewTotal) * 100 }}%;"></div>
                                            </div>
                                            <span class="w-6 text-right">{{ $count }}</span>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-4 space-y-3">
                                    @forelse ($doctor->reviews->take(3) as $review)
                                        <div class="bg-white rounded-[1.2rem] p-4 border border-slate-100">
                                            <div class="flex items-center justify-between gap-3">
                                                <p class="font-bold text-dark text-sm">{{ $review->user->name }}</p>
                                                <span class="text-amber-500 text-sm font-extrabold">{{ $review->rating }}/5</span>
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
                                        <button type="submit" class="sm:col-span-2 border border-secondary/20 text-secondary rounded-[1.2rem] py-3 font-bold hover:bg-secondary hover:text-white transition-colors">
                                            {{ $myReview ? 'Update Review' : 'Submit Review' }}
                                        </button>
                                    </form>
                                @else
                                    <p class="text-xs text-slate-500 mt-4">You can review this doctor after booking an appointment.</p>
                                @endif
                            </div>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="inline-flex mt-5 text-primary font-semibold items-center gap-2">Login to book appointment <i class="ph-bold ph-arrow-right"></i></a>
                    @endauth
                </div>
            @empty
                <div class="lg:col-span-2 bento-card rounded-[1.9rem] p-10 text-center">
                    <div class="w-16 h-16 mx-auto rounded-[1.2rem] bg-slate-50 text-primary flex items-center justify-center text-3xl">
                        <i class="ph-fill ph-user-focus"></i>
                    </div>
                    <h2 class="text-2xl font-extrabold text-dark mt-5">No doctors listed yet</h2>
                    <p class="text-slate-500 mt-2">This clinic is verified, but their doctors and schedules have not been published yet.</p>
                </div>
            @endforelse
        </div>
    </section>

    <script>
        document.querySelectorAll('.doctor-schedule-tab').forEach((tab) => {
            tab.addEventListener('click', () => {
                const doctorId = tab.dataset.doctorTab.split('-')[0];

                document.querySelectorAll(`[data-doctor-tab^="${doctorId}-"]`).forEach((item) => {
                    item.className = 'doctor-schedule-tab px-3 py-2 rounded-full border text-xs font-bold transition-all bg-white border-slate-200 text-dark hover:border-primary/40';
                });

                document.querySelectorAll(`[data-doctor-panel^="${doctorId}-"]`).forEach((panel) => {
                    panel.classList.add('hidden');
                });

                tab.className = 'doctor-schedule-tab px-3 py-2 rounded-full border text-xs font-bold transition-all bg-dark text-white border-dark';
                document.querySelector(`[data-doctor-panel="${tab.dataset.doctorTab}"]`)?.classList.remove('hidden');
            });
        });
    </script>
@endsection
