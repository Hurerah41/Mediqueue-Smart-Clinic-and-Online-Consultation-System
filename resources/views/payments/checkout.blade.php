@extends('layouts.app', ['title' => 'Payment Checkout | MediQueue'])

@section('content')
    <section class="max-w-4xl mx-auto">
        <div class="dashboard-card rounded-[2rem] p-5 sm:p-8">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] font-black text-primary">Secure Checkout</p>
                    <h1 class="mt-3 text-3xl sm:text-5xl font-black tracking-tight text-dark">Confirm online consultation</h1>
                    <p class="mt-4 text-sm sm:text-base leading-7 text-slate-600 font-medium">
                        Pay the doctor consultation fee to confirm your online token. In local development this uses a test checkout; production can use JazzCash, PayFast, or Safepay from the same payment record.
                    </p>
                </div>
                <div class="rounded-[1.5rem] bg-slate-50 border border-slate-100 p-5 min-w-0 lg:min-w-[240px]">
                    <p class="text-xs uppercase tracking-[0.18em] font-black text-slate-400">Amount</p>
                    <p class="mt-2 text-4xl font-black text-dark">Rs {{ number_format($payment->amount) }}</p>
                    <p class="mt-2 text-sm font-bold text-slate-500">{{ $payment->currency }} via {{ $gatewayLabel }}</p>
                </div>
            </div>

            <div class="mt-8 grid md:grid-cols-3 gap-4">
                <div class="rounded-3xl bg-blue-50 p-5">
                    <p class="text-xs uppercase tracking-[0.18em] font-black text-slate-500">Doctor</p>
                    <p class="mt-2 text-lg font-black text-dark">Dr. {{ $payment->appointment->doctor->user->name }}</p>
                    <p class="text-sm font-medium text-slate-500">{{ $payment->appointment->doctor->specialization->name }}</p>
                </div>
                <div class="rounded-3xl bg-purple-50 p-5">
                    <p class="text-xs uppercase tracking-[0.18em] font-black text-slate-500">Clinic</p>
                    <p class="mt-2 text-lg font-black text-dark">{{ $payment->clinic->name }}</p>
                    <p class="text-sm font-medium text-slate-500">{{ $payment->clinic->address }}</p>
                </div>
                <div class="rounded-3xl bg-green-50 p-5">
                    <p class="text-xs uppercase tracking-[0.18em] font-black text-slate-500">Appointment</p>
                    <p class="mt-2 text-lg font-black text-dark">{{ $payment->appointment->appointment_date->format('d M Y') }}</p>
                    <p class="text-sm font-medium text-slate-500">{{ $payment->appointment->appointment_time ?: 'Queue token' }}</p>
                </div>
            </div>

            @if ($payment->status === \App\Models\AppointmentPayment::STATUS_PAID)
                <div class="mt-8 rounded-[1.5rem] border border-green-200 bg-green-50 p-5 text-green-700 font-bold">
                    Payment already completed.
                </div>
                <a href="{{ route('appointments.show', $payment->appointment) }}" class="mt-5 inline-flex w-full sm:w-auto justify-center rounded-2xl bg-dark px-6 py-3 font-bold text-white hover:bg-primary transition-colors">
                    Open Appointment
                </a>
            @elseif ($payment->status === \App\Models\AppointmentPayment::STATUS_CANCELLED)
                <div class="mt-8 rounded-[1.5rem] border border-rose-200 bg-rose-50 p-5 text-rose-700 font-bold">
                    This checkout was cancelled. Please book again.
                </div>
            @else
                <div class="mt-8 grid sm:grid-cols-2 gap-3">
                    <form method="POST" action="{{ route('payments.confirm', $payment) }}">
                        @csrf
                        <button type="submit" class="w-full rounded-2xl bg-gradient-premium px-6 py-4 font-black text-white shadow-glow">
                            Pay Rs {{ number_format($payment->amount) }} and Confirm Token
                        </button>
                    </form>
                    <form method="POST" action="{{ route('payments.cancel', $payment) }}">
                        @csrf
                        <button type="submit" class="w-full rounded-2xl border border-slate-200 bg-white px-6 py-4 font-black text-slate-600 hover:border-rose-200 hover:text-rose-600 transition-colors">
                            Cancel Payment
                        </button>
                    </form>
                </div>
            @endif

            <div class="mt-8 rounded-[1.5rem] bg-slate-50 border border-slate-100 p-5">
                <p class="text-sm font-black text-dark">Production note</p>
                <p class="mt-2 text-sm leading-7 text-slate-600 font-medium">
                    This screen is the local test gateway. When you receive merchant credentials, replace the confirm button action with the selected provider checkout redirect and call the same confirmation service from the provider callback/webhook.
                </p>
            </div>
        </div>
    </section>
@endsection
