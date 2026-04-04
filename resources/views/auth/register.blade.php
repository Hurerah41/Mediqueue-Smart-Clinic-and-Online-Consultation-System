@extends('layouts.app', ['title' => 'Register | MediQueue'])

@section('content')
    <div class="max-w-5xl mx-auto grid lg:grid-cols-2 gap-8 items-center">
        <div class="glass-panel rounded-[2rem] p-10 shadow-glass">
            <div class="w-14 h-14 rounded-2xl bg-gradient-premium flex items-center justify-center text-white shadow-glow mb-6">
                <i class="ph-bold ph-user-plus text-3xl"></i>
            </div>
            <p class="text-sm font-bold text-primary uppercase tracking-widest mb-3">Create patient account</p>
            <h1 class="text-5xl font-extrabold tracking-tight text-dark mb-4">Book clinic tokens and consult doctors online.</h1>
            <p class="text-slate-600 leading-relaxed">Browse clinics by area, reserve queue tokens, receive digital prescriptions, and use the AI assistant from your dashboard.</p>
        </div>

        <div class="bg-white rounded-[2rem] shadow-soft border border-slate-100 p-8">
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
                <button type="submit" class="w-full bg-gradient-premium text-white rounded-2xl py-4 font-bold shadow-glow hover:scale-[1.01] transition-all">Create Account</button>
                <p class="text-sm text-slate-500 text-center">Already registered? <a href="{{ route('login') }}" class="text-primary font-semibold">Login</a></p>
            </form>
        </div>
    </div>
@endsection
