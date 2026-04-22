@extends('layouts.app', ['title' => 'Forgot Password | MediQueue'])

@section('content')
    <div class="w-[97%] max-w-3xl mx-auto auth-shell rounded-[2.25rem] p-7 sm:p-9">
        <div class="max-w-2xl">
            <div class="hero-badge inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold text-slate-500 mb-5">
                <span class="w-2 h-2 rounded-full bg-accent animate-pulse-soft"></span>
                Password recovery
            </div>
            <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-dark leading-[1.03]">Get a reset code on your email.</h1>
            <p class="mt-5 text-base leading-8 text-slate-600 font-medium">Enter your account email and we will send a 6-digit reset code valid for 10 minutes.</p>
        </div>

        <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-5 bg-white rounded-[1.8rem] border border-slate-100 p-6">
            @csrf
            <div>
                <label class="block text-sm font-semibold mb-2 text-dark">Email Address</label>
                <input name="email" type="email" value="{{ old('email') }}" class="form-control" required>
            </div>
            <button type="submit" class="w-full bg-dark text-white rounded-[1.2rem] py-4 font-bold hover:bg-primary transition-colors">Send Reset Code</button>
            <p class="text-sm text-slate-500 text-center">
                Remember your password?
                <a href="{{ route('login') }}" class="text-primary font-semibold">Back to login</a>
            </p>
        </form>
    </div>
@endsection
