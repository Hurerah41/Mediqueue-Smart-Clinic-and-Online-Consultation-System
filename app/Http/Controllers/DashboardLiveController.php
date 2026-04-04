<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\ClinicApplication;
use App\Models\Prescription;
use App\Models\QueueToken;
use App\Models\User;
use App\Services\QueueManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardLiveController extends Controller
{
    public function __invoke(Request $request, QueueManagementService $queueService): JsonResponse
    {
        $user = $request->user();

        if ($user->isSuperAdmin()) {
            return response()->json([
                'stats' => [
                    'clinics' => Clinic::count(),
                    'pending_apps' => ClinicApplication::where('status', ClinicApplication::STATUS_PENDING)->count(),
                    'clinic_admins' => User::where('role', User::ROLE_ADMIN)->count(),
                    'doctors' => User::where('role', User::ROLE_DOCTOR)->count(),
                    'patients' => User::where('role', User::ROLE_PATIENT)->count(),
                ],
                'clinic_applications' => ClinicApplication::with('area')
                    ->where('status', ClinicApplication::STATUS_PENDING)
                    ->latest()
                    ->get()
                    ->map(fn (ClinicApplication $application) => [
                        'id' => $application->id,
                        'clinic_name' => $application->clinic_name,
                        'area_name' => $application->area->name,
                        'address' => $application->address,
                        'clinic_phone' => $application->clinic_phone,
                        'admin_name' => $application->admin_name,
                        'admin_email' => $application->admin_email,
                        'admin_phone' => $application->admin_phone,
                        'approve_url' => route('owner.clinic-applications.approve', $application),
                        'reject_url' => route('owner.clinic-applications.reject', $application),
                    ]),
                'clinics' => Clinic::with(['area', 'users'])
                    ->latest()
                    ->get()
                    ->map(fn (Clinic $clinic) => [
                        'id' => $clinic->id,
                        'name' => $clinic->name,
                        'area_name' => $clinic->area->name,
                        'address' => $clinic->address,
                        'admin_count' => $clinic->users->where('role', User::ROLE_ADMIN)->count(),
                        'doctor_count' => $clinic->users->where('role', User::ROLE_DOCTOR)->count(),
                        'is_active' => $clinic->is_active,
                    ]),
                'users' => User::with('clinic')
                    ->latest()
                    ->get()
                    ->map(fn (User $member) => [
                        'id' => $member->id,
                        'name' => $member->name,
                        'email' => $member->email,
                        'phone' => $member->phone,
                        'role' => $member->role,
                        'clinic_id' => $member->clinic_id,
                        'clinic_name' => $member->clinic?->name ?? 'No clinic',
                        'is_owner' => $member->isSuperAdmin(),
                    ]),
            ]);
        }

        if ($user->isAdmin()) {
            return response()->json([
                'stats' => [
                    'doctors' => User::where('clinic_id', $user->clinic_id)->where('role', User::ROLE_DOCTOR)->count(),
                    'appointments_today' => Appointment::where('clinic_id', $user->clinic_id)
                        ->whereDate('appointment_date', now()->toDateString())
                        ->count(),
                    'appointments_month' => Appointment::where('clinic_id', $user->clinic_id)
                        ->whereBetween('appointment_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
                        ->count(),
                    'waiting_tokens' => QueueToken::where('clinic_id', $user->clinic_id)
                        ->where('status', QueueToken::STATUS_WAITING)
                        ->count(),
                    'prescriptions' => Prescription::whereHas('doctor', fn ($query) => $query->where('clinic_id', $user->clinic_id))->count(),
                ],
            ]);
        }

        if ($user->isDoctor()) {
            return response()->json(['stats' => []]);
        }

        $appointments = Appointment::query()
            ->with(['clinic.area', 'doctor.user', 'doctor.specialization', 'queueToken', 'prescription'])
            ->where('patient_id', $user->id)
            ->latest()
            ->take(10)
            ->get()
            ->map(function (Appointment $appointment) use ($queueService) {
                $queuePosition = $queueService->queuePosition($appointment);
                $currentlyServingToken = QueueToken::query()
                    ->where('doctor_id', $appointment->doctor_id)
                    ->whereDate('queue_date', $appointment->appointment_date->toDateString())
                    ->where('status', QueueToken::STATUS_CALLED)
                    ->orderBy('token_number')
                    ->value('token_number');

                $tokenNumber = $appointment->queueToken?->token_number;
                $progressPercent = match ($appointment->queueToken?->status) {
                    QueueToken::STATUS_COMPLETED => 100,
                    QueueToken::STATUS_CALLED => 92,
                    QueueToken::STATUS_WAITING => max(12, min(88, 88 - ($queuePosition * 18))),
                    default => 10,
                };

                return [
                    'id' => $appointment->id,
                    'doctor_name' => $appointment->doctor->user->name,
                    'specialization' => $appointment->doctor->specialization->name,
                    'clinic_name' => $appointment->clinic->name,
                    'area_name' => $appointment->clinic->area->name,
                    'date' => $appointment->appointment_date->format('d M Y'),
                    'consultation_type' => ucfirst($appointment->consultation_type),
                    'token_number' => $tokenNumber,
                    'currently_serving_token' => $currentlyServingToken,
                    'queue_position' => $queuePosition,
                    'eta_seconds' => max(0, (int) $queuePosition * (int) ($appointment->doctor->avg_consultation_minutes ?? 15) * 60),
                    'progress_percent' => $progressPercent,
                    'token_status' => $appointment->queueToken?->status,
                    'appointment_status' => $appointment->status,
                    'details_url' => route('appointments.show', $appointment),
                    'prescription_pdf_url' => $appointment->prescription ? route('prescriptions.download', $appointment->prescription) : null,
                    'prescription_image_url' => $appointment->prescription ? route('prescriptions.image', $appointment->prescription) : null,
                ];
            });

        return response()->json(['appointments' => $appointments]);
    }
}
