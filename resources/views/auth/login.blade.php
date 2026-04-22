@extends('layouts.app', ['title' => 'Login | MediQueue'])

@section('content')
    <div class="w-[97%] max-w-6xl mx-auto grid lg:grid-cols-[1fr_0.9fr] gap-6 items-stretch">
        <div class="auth-shell rounded-[2.25rem] p-8 sm:p-10 flex flex-col justify-between">
            <div>
                <div class="hero-badge inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold text-slate-500 mb-5">
                    <span class="w-2 h-2 rounded-full bg-accent animate-pulse-soft"></span>
                    Secure account access
                </div>
                <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-dark leading-[1.02]">Log in to manage appointments, queues, and consultations.</h1>
                <p class="mt-5 text-base leading-8 text-slate-600 font-medium">Patients, doctors, clinic admins, and the platform owner all use one secure sign-in flow with verification and real-time updates.</p>
            </div>

            <div class="mt-8 surface-white rounded-[1.75rem] p-6">
                <p class="text-[11px] uppercase tracking-[0.22em] font-black text-slate-400">Demo access</p>
                <div class="mt-4 space-y-3 text-sm text-slate-600 font-medium">
                    <div><span class="font-bold text-dark">Owner:</span> owner@mediqueue.test / password</div>
                    <div><span class="font-bold text-dark">Admin:</span> admin@smartclinic.test / password</div>
                    <div><span class="font-bold text-dark">Doctor:</span> doctor@smartclinic.test / password</div>
                    <div><span class="font-bold text-dark">Patient:</span> patient@smartclinic.test / password</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2.25rem] shadow-soft border border-slate-100 p-7 sm:p-8">
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-semibold mb-2 text-dark">Email Address</label>
                    <input name="email" type="email" value="{{ old('email') }}" class="form-control" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2 text-dark">Password</label>
                    <input name="password" type="password" class="form-control" required>
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-600 font-medium">
                    <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-primary" checked>
                    Keep me signed in until I log out
                </label>
                <button type="submit" class="w-full bg-dark text-white rounded-[1.2rem] py-4 font-bold hover:bg-primary transition-colors">Login</button>
                <p class="text-sm text-center">
                    <a href="{{ route('password.request') }}" class="text-secondary font-semibold">Forgot password?</a>
                </p>
                <p class="text-sm text-slate-500 text-center">New patient? <a href="{{ route('register') }}" class="text-primary font-semibold">Create account</a></p>
            </form>
        </div>
    </div>
@endsection
