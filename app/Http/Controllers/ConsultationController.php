<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsultationController extends Controller
{
    public function show(Request $request, Consultation $consultation): View
    {
        $this->authorizeConsultationAccess($request, $consultation);

        return view('consultations.show', [
            'consultation' => $consultation->load(['appointment.clinic', 'appointment.doctor.user', 'messages.sender', 'patient']),
        ]);
    }

    public function messages(Request $request, Consultation $consultation): JsonResponse
    {
        $this->authorizeConsultationAccess($request, $consultation);

        $consultation->load(['messages.sender']);

        return response()->json([
            'status' => $consultation->status,
            'messages' => $consultation->messages->map(fn ($message) => [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'sender_name' => $message->sender->name,
                'message' => $message->message,
                'sent_at' => $message->created_at->format('h:i A'),
            ]),
        ]);
    }

    public function sendMessage(Request $request, Consultation $consultation): RedirectResponse|JsonResponse
    {
        $this->authorizeConsultationAccess($request, $consultation);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $message = $consultation->messages()->create([
            'sender_id' => $request->user()->id,
            'message' => $validated['message'],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'sender_name' => $request->user()->name,
                'message' => $message->message,
                'sent_at' => $message->created_at->format('h:i A'),
            ]);
        }

        return back();
    }

    private function authorizeConsultationAccess(Request $request, Consultation $consultation): void
    {
        $user = $request->user();

        abort_unless(
            $consultation->patient_id === $user->id
            || ($user->doctorProfile && $consultation->doctor_id === $user->doctorProfile->id)
            || ($user->isAdmin() && $consultation->appointment?->clinic_id === $user->clinic_id),
            403
        );
    }
}
