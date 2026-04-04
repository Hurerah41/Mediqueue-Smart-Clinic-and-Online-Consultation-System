@extends('layouts.app', ['title' => 'Login | MediQueue'])

@section('content')
    <div class="max-w-5xl mx-auto grid lg:grid-cols-2 gap-8 items-center">
        <div class="glass-panel rounded-[2rem] p-10 shadow-glass">
            <p class="text-sm font-bold text-primary uppercase tracking-widest mb-3">Welcome back</p>
            <h1 class="text-5xl font-extrabold tracking-tight text-dark mb-4">Log in to manage your queue and consultations.</h1>
            <p class="text-slate-600 leading-relaxed mb-8">Patients, doctors, and clinic admins all access their live dashboard from one secure entry point.</p>

            <div class="bg-white rounded-3xl p-5 shadow-soft border border-slate-100">
                <p class="text-sm font-bold text-dark mb-3">Demo Accounts</p>
                <div class="space-y-2 text-sm text-slate-600">
                    <div><strong>Owner:</strong> owner@mediqueue.test / password</div>
                    <div><strong>Admin:</strong> admin@smartclinic.test / password</div>
                    <div><strong>Doctor:</strong> doctor@smartclinic.test / password</div>
                    <div><strong>Patient:</strong> patient@smartclinic.test / password</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] shadow-soft border border-slate-100 p-8">
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
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-primary">
                    Remember me
                </label>
                <button type="submit" class="w-full bg-gradient-premium text-white rounded-2xl py-4 font-bold shadow-glow hover:scale-[1.01] transition-all">Login</button>
                <p class="text-sm text-center">
                    <a href="{{ route('password.request') }}" class="text-secondary font-semibold">Forgot password?</a>
                </p>
                <p class="text-sm text-slate-500 text-center">New patient? <a href="{{ route('register') }}" class="text-primary font-semibold">Create account</a></p>
            </form>
        </div>
    </div>
@endsection
