@extends('layouts.app', ['title' => 'Register Clinic | MediQueue'])

@section('content')
    <div class="max-w-5xl mx-auto grid lg:grid-cols-2 gap-8 items-start">
        <div class="glass-panel rounded-[2rem] p-10 shadow-glass">
            <p class="text-sm font-bold text-primary uppercase tracking-widest mb-3">Clinic Partner Registration</p>
            <h1 class="text-5xl font-extrabold tracking-tight text-dark mb-4">Apply to list your clinic on MediQueue.</h1>
            <p class="text-slate-600 leading-relaxed">Submit your clinic details and admin contact. The platform owner reviews your application and approves verified clinics from the owner dashboard.</p>
        </div>

        <div class="bg-white rounded-[2rem] shadow-soft border border-slate-100 p-8">
            <form method="POST" action="{{ route('clinic-applications.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <select name="area_id" class="form-control" required>
                    <option value="">Select clinic area</option>
                    @foreach ($areas as $area)
                        <option value="{{ $area->id }}">{{ $area->name }}, {{ $area->city }}</option>
                    @endforeach
                </select>
                <input name="clinic_name" class="form-control" placeholder="Clinic name" required>
                <input name="clinic_phone" class="form-control" placeholder="Clinic phone">
                <input name="address" class="form-control" placeholder="Clinic address" required>
                <div class="grid sm:grid-cols-2 gap-4">
                    <input type="time" name="opens_at" class="form-control">
                    <input type="time" name="closes_at" class="form-control">
                </div>
                <input name="admin_name" class="form-control" placeholder="Clinic admin full name" required>
                <input type="email" name="admin_email" class="form-control" placeholder="Clinic admin email" required>
                <input name="admin_phone" class="form-control" placeholder="Clinic admin phone">
                <div>
                    <label class="block text-sm font-semibold mb-2 text-dark">Clinic Logo</label>
                    <input type="file" name="clinic_logo" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                </div>
                <input type="file" name="license_document" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                <button type="submit" class="w-full bg-gradient-premium text-white rounded-2xl py-4 font-bold shadow-glow">Submit Application</button>
            </form>
        </div>
    </div>
@endsection
