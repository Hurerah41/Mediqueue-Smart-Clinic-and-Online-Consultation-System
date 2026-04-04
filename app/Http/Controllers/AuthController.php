<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class AuthController extends Controller
{
    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            ...$validated,
            'role' => User::ROLE_PATIENT,
            'otp_code' => Hash::make($otpCode = (string) random_int(100000, 999999)),
            'otp_expires_at' => now()->addMinutes(10),
            'is_verified' => false,
        ]);

        if (! $this->sendPlainMail(
            $user->email,
            'MediQueue Account Verification Code',
            "Your MediQueue verification code is: {$otpCode}\nThis code expires in 10 minutes."
        )) {
            $user->delete();

            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->withErrors(['email' => 'Unable to send verification email right now. Please check Gmail SMTP settings and try again.']);
        }

        $request->session()->put('pending_otp_email', $user->email);

        return redirect()->route('verify-otp')->with('success', 'Verification code sent to your email.');
    }

    public function showVerifyOtp(Request $request): View
    {
        return view('auth.verify-otp', [
            'email' => old('email', $request->session()->get('pending_otp_email')),
        ]);
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'otp_code' => ['required', 'digits:6'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || $user->is_verified) {
            return back()->withErrors(['email' => 'Unable to verify this account.']);
        }

        if (! $user->otp_code || ! $user->otp_expires_at || now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors(['otp_code' => 'Verification code has expired. Please request a new one.']);
        }

        if (! Hash::check($validated['otp_code'], $user->otp_code)) {
            return back()->withErrors(['otp_code' => 'Invalid verification code.']);
        }

        $user->update([
            'otp_code' => null,
            'otp_expires_at' => null,
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);

        $request->session()->forget('pending_otp_email');
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Account verified successfully.');
    }

    public function resendOtp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || $user->is_verified) {
            return back()->withErrors(['email' => 'Unable to resend code for this account.']);
        }

        $otpCode = (string) random_int(100000, 999999);
        $user->update([
            'otp_code' => Hash::make($otpCode),
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        if (! $this->sendPlainMail(
            $user->email,
            'MediQueue New Verification Code',
            "Your new MediQueue verification code is: {$otpCode}\nThis code expires in 10 minutes."
        )) {
            return back()->withErrors(['email' => 'Unable to send verification code right now. Please check Gmail SMTP settings and try again.']);
        }

        $request->session()->put('pending_otp_email', $user->email);

        return back()->with('success', 'New verification code sent.');
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    public function sendPasswordResetCode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return back()->withErrors(['email' => 'No account found with this email address.']);
        }

        $resetCode = (string) random_int(100000, 999999);

        $user->update([
            'password_reset_code' => Hash::make($resetCode),
            'password_reset_expires_at' => now()->addMinutes(10),
        ]);

        if (! $this->sendPlainMail(
            $user->email,
            'MediQueue Password Reset Code',
            "Your MediQueue password reset code is: {$resetCode}\nThis code expires in 10 minutes."
        )) {
            return back()->withErrors(['email' => 'Unable to send reset code right now. Please check Gmail SMTP settings and try again.']);
        }

        $request->session()->put('password_reset_email', $user->email);

        return redirect()->route('password.reset.form')->with('success', 'Password reset code sent to your email.');
    }

    public function showResetPassword(Request $request): View
    {
        return view('auth.reset-password', [
            'email' => old('email', $request->session()->get('password_reset_email')),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'reset_code' => ['required', 'digits:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (
            ! $user ||
            ! $user->password_reset_code ||
            ! $user->password_reset_expires_at ||
            now()->greaterThan($user->password_reset_expires_at)
        ) {
            return back()->withErrors(['reset_code' => 'Reset code is invalid or expired.']);
        }

        if (! Hash::check($validated['reset_code'], $user->password_reset_code)) {
            return back()->withErrors(['reset_code' => 'Incorrect reset code.']);
        }

        $user->update([
            'password' => $validated['password'],
            'password_reset_code' => null,
            'password_reset_expires_at' => null,
            'otp_code' => null,
            'otp_expires_at' => null,
            'is_verified' => true,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ]);

        $request->session()->forget('password_reset_email');

        return redirect()->route('login')->with('success', 'Password updated. Please log in with your new password.');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
        }

        $authUser = Auth::user();

        if (! $authUser?->is_verified) {
            $email = $authUser?->email;
            Auth::logout();
            if ($email) {
                $request->session()->put('pending_otp_email', $email);
            }

            return redirect()->route('verify-otp')->withErrors(['email' => 'Please verify your account first.']);
        }

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function sendPlainMail(string $toEmail, string $subject, string $body): bool
    {
        try {
            Mail::raw($body, fn ($message) => $message->to($toEmail)->subject($subject));

            return true;
        } catch (Throwable $exception) {
            Log::error('MediQueue email sending failed', [
                'to' => $toEmail,
                'subject' => $subject,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
