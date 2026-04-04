@extends('layouts.app', ['title' => 'Verify Account | MediQueue'])

@section('content')
    <div class="max-w-xl mx-auto bg-white rounded-[2rem] shadow-soft border border-slate-100 p-8">
        <p class="text-sm font-bold text-primary uppercase tracking-widest mb-3">Verify Account</p>
        <h1 class="text-4xl font-extrabold tracking-tight text-dark mb-4">Enter your 6-digit code</h1>
        <p class="text-slate-600 mb-8">We sent a verification code to your email. Enter it below to activate your patient account.</p>

        <form method="POST" action="{{ route('verify-otp.store') }}" class="space-y-4">
            @csrf
            <input type="email" name="email" class="form-control" value="{{ $email }}" placeholder="Email address" required>
            <input type="text" name="otp_code" class="form-control text-center tracking-[0.5em] font-extrabold" maxlength="6" placeholder="000000" required>
            <button type="submit" class="w-full bg-gradient-premium text-white rounded-2xl py-4 font-bold shadow-glow">Verify & Continue</button>
        </form>

        <form method="POST" action="{{ route('verify-otp.resend') }}" class="mt-4">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <button type="submit" class="w-full border border-primary/20 text-primary rounded-2xl py-3 font-bold hover:bg-primary hover:text-white transition-all">Resend Code</button>
        </form>

        <p class="text-xs text-slate-500 mt-6">For local development, email is sent through your configured Laravel mail driver, currently usually `log`.</p>
    </div>
@endsection
