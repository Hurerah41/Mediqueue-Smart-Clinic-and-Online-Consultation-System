@extends('layouts.app', ['title' => 'Reset Password | MediQueue'])

@section('content')
    <div class="max-w-2xl mx-auto bg-white rounded-[2rem] shadow-soft border border-slate-100 p-8">
        <p class="text-sm font-bold text-secondary uppercase tracking-widest mb-3">Set New Password</p>
        <h1 class="text-4xl font-extrabold tracking-tight text-dark mb-4">Verify reset code and create a new password.</h1>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
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
            <button type="submit" class="w-full bg-dark text-white rounded-2xl py-4 font-bold hover:bg-slate-800 transition-all">Update Password</button>
        </form>
    </div>
@endsection
