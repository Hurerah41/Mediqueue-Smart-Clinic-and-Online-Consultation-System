@extends('layouts.app', ['title' => 'Register | MediQueue'])

@section('content')
    <div class="w-[97%] max-w-6xl mx-auto grid lg:grid-cols-[1fr_0.9fr] gap-6 items-stretch">
        <div class="auth-shell rounded-[2.25rem] p-8 sm:p-10 flex flex-col justify-between">
            <div>
                <div class="hero-badge inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold text-slate-500 mb-5">
                    <span class="w-2 h-2 rounded-full bg-accent animate-pulse-soft"></span>
                    Patient account setup
                </div>
                <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-dark leading-[1.02]">Create your account and start booking care faster.</h1>
                <p class="mt-5 text-base leading-8 text-slate-600 font-medium">Search clinics, join a live queue before leaving home, receive alerts when your turn is close, and download prescriptions after your visit.</p>
            </div>

            <div class="mt-8 grid sm:grid-cols-2 gap-4">
                <div class="surface-white rounded-[1.5rem] p-5">
                    <p class="text-sm font-extrabold text-dark">Verified clinics</p>
                    <p class="mt-2 text-sm text-slate-500 font-medium">Browse approved healthcare centers with visible timings and doctors.</p>
                </div>
                <div class="surface-white rounded-[1.5rem] p-5">
                    <p class="text-sm font-extrabold text-dark">Real-time updates</p>
                    <p class="mt-2 text-sm text-slate-500 font-medium">Know when the doctor calls your token and when your prescription is ready.</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2.25rem] shadow-soft border border-slate-100 p-7 sm:p-8">
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-semibold mb-2 text-dark">Full Name</label>
                    <input name="name" value="{{ old('name') }}" class="form-control" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2 text-dark">Phone</label>
                    <input name="phone" value="{{ old('phone') }}" class="form-control">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2 text-dark">Email Address</label>
                    <input name="email" type="email" value="{{ old('email') }}" class="form-control" required>
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-dark">Password</label>
                        <input name="password" type="password" class="form-control" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-dark">Confirm Password</label>
                        <input name="password_confirmation" type="password" class="form-control" required>
                    </div>
                </div>
                <button type="submit" class="w-full bg-dark text-white rounded-[1.2rem] py-4 font-bold hover:bg-primary transition-colors">Create Account</button>
                <p class="text-sm text-slate-500 text-center">Already registered? <a href="{{ route('login') }}" class="text-primary font-semibold">Login</a></p>
            </form>
        </div>
    </div>
@endsection
