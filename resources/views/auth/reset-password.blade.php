@extends('layouts.app', ['title' => 'Reset Password | MediQueue'])

@section('content')
    <div class="w-[97%] max-w-3xl mx-auto auth-shell rounded-[2.25rem] p-7 sm:p-9">
        <div class="max-w-2xl">
            <div class="hero-badge inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold text-slate-500 mb-5">
                <span class="w-2 h-2 rounded-full bg-accent animate-pulse-soft"></span>
                Set new password
            </div>
            <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-dark leading-[1.03]">Verify the reset code and create a new password.</h1>
        </div>

        <form method="POST" action="{{ route('password.update') }}" class="mt-8 space-y-5 bg-white rounded-[1.8rem] border border-slate-100 p-6">
            @csrf
            <div>
                <label class="block text-sm font-semibold mb-2 text-dark">Email Address</label>
                <input name="email" type="email" value="{{ $email }}" class="form-control" required>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-2 text-dark">Reset Code</label>
                <input name="reset_code" inputmode="numeric" maxlength="6" class="form-control" placeholder="6-digit code" required>
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-2 text-dark">New Password</label>
                    <input name="password" type="password" class="form-control" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2 text-dark">Confirm Password</label>
                    <input name="password_confirmation" type="password" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="w-full bg-dark text-white rounded-[1.2rem] py-4 font-bold hover:bg-primary transition-colors">Update Password</button>
        </form>
    </div>
@endsection
