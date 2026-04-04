@extends('layouts.app', ['title' => 'Forgot Password | MediQueue'])

@section('content')
    <div class="max-w-2xl mx-auto bg-white rounded-[2rem] shadow-soft border border-slate-100 p-8">
        <p class="text-sm font-bold text-primary uppercase tracking-widest mb-3">Password Recovery</p>
        <h1 class="text-4xl font-extrabold tracking-tight text-dark mb-4">Get a reset code on your email.</h1>
        <p class="text-slate-600 mb-8">Enter your account email and we’ll send a 6-digit reset code valid for 10 minutes.</p>

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-semibold mb-2 text-dark">Email Address</label>
                <input name="email" type="email" value="{{ old('email') }}" class="form-control" required>
            </div>
            <button type="submit" class="w-full bg-gradient-premium text-white rounded-2xl py-4 font-bold shadow-glow">Send Reset Code</button>
            <p class="text-sm text-slate-500 text-center">
                Remember your password?
                <a href="{{ route('login') }}" class="text-primary font-semibold">Back to login</a>
            </p>
        </form>
    </div>
@endsection
