<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Appointment;
use App\Models\Prescription;
use App\Services\NotificationService;
use App\Services\PrescriptionPdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class PrescriptionController extends Controller
{
    public function store(Request $request, Appointment $appointment, NotificationService $notificationService): RedirectResponse
    {
        $doctor = $request->user()->doctorProfile;
        abort_unless($doctor && $appointment->doctor_id === $doctor->id, 403);

        $validated = $request->validate([
            'diagnosis' => ['required', 'string', 'max:4000'],
            'notes' => ['nullable', 'string', 'max:4000'],
            'medicine_name' => ['required', 'array', 'min:1'],
            'medicine_name.*' => ['required', 'string', 'max:255'],
            'dosage' => ['required', 'array', 'min:1'],
            'dosage.*' => ['required', 'string', 'max:255'],
            'frequency' => ['required', 'array', 'min:1'],
            'frequency.*' => ['required', 'string', 'max:255'],
            'duration' => ['required', 'array', 'min:1'],
            'duration.*' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated, $appointment, $doctor): void {
            $prescription = Prescription::updateOrCreate(
                ['appointment_id' => $appointment->id],
                [
                    'doctor_id' => $doctor->id,
                    'patient_id' => $appointment->patient_id,
                    'diagnosis' => $validated['diagnosis'],
                    'notes' => $validated['notes'] ?? null,
                    'issued_at' => now(),
                ]
            );

            $prescription->items()->delete();

            foreach ($validated['medicine_name'] as $index => $medicineName) {
                $prescription->items()->create([
                    'medicine_name' => $medicineName,
                    'dosage' => $validated['dosage'][$index],
                    'frequency' => $validated['frequency'][$index],
                    'duration' => $validated['duration'][$index],
                ]);
            }
        });

        $notificationService->createForAppointment(
            $appointment,
            $appointment->patient_id,
            AppNotification::TYPE_PRESCRIPTION_READY,
            'Prescription is ready',
            'Dr. '.$doctor->user->name.' has issued your digital prescription. Tap to download it.'
        );

        return back()->with('success', 'Prescription saved successfully.');
    }

    public function download(Request $request, Prescription $prescription, PrescriptionPdfService $pdfService): Response
    {
        $this->authorizePrescriptionAccess($request, $prescription);

        return response($pdfService->render($prescription), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="prescription-'.$prescription->id.'.pdf"',
        ]);
    }

    public function downloadImage(Request $request, Prescription $prescription): Response
    {
        $this->authorizePrescriptionAccess($request, $prescription);
        $prescription->load(['patient', 'doctor.user', 'doctor.specialization', 'appointment.clinic', 'items']);

        $medicineRows = $prescription->items->values()->map(function ($item, int $index): string {
            $y = 430 + ($index * 58);
            $metaY = $y + 28;
            $medicine = htmlspecialchars($item->medicine_name, ENT_QUOTES, 'UTF-8');
            $meta = htmlspecialchars($item->dosage.' | '.$item->frequency.' | '.$item->duration, ENT_QUOTES, 'UTF-8');

            return <<<SVG
                <text x="80" y="{$y}" font-family="Inter, Arial, sans-serif" font-size="22" font-weight="700" fill="#0F172A">{$medicine}</text>
                <text x="80" y="{$metaY}" font-family="Inter, Arial, sans-serif" font-size="16" fill="#64748B">{$meta}</text>
            SVG;
        })->implode("\n");

        $height = max(900, 560 + ($prescription->items->count() * 70));
        $cardHeight = $height - 80;
        $notesLabelY = $height - 165;
        $notesValueY = $height - 130;
        $issuedAtY = $height - 85;
        $patientName = htmlspecialchars($prescription->patient->name, ENT_QUOTES, 'UTF-8');
        $doctorName = htmlspecialchars('Dr. '.$prescription->doctor->user->name, ENT_QUOTES, 'UTF-8');
        $specialization = htmlspecialchars($prescription->doctor->specialization->name, ENT_QUOTES, 'UTF-8');
        $clinicName = htmlspecialchars($prescription->appointment?->clinic?->name ?? 'MediQueue Clinic', ENT_QUOTES, 'UTF-8');
        $diagnosis = htmlspecialchars($prescription->diagnosis, ENT_QUOTES, 'UTF-8');
        $notes = htmlspecialchars($prescription->notes ?? 'Follow your doctor instructions and schedule a follow-up if needed.', ENT_QUOTES, 'UTF-8');
        $issuedAt = htmlspecialchars($prescription->issued_at?->format('d M Y, h:i A') ?? now()->format('d M Y, h:i A'), ENT_QUOTES, 'UTF-8');

        $svg = <<<SVG
            <svg xmlns="http://www.w3.org/2000/svg" width="1200" height="{$height}" viewBox="0 0 1200 {$height}">
                <defs>
                    <linearGradient id="rxGradient" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stop-color="#2563EB"/>
                        <stop offset="100%" stop-color="#7C3AED"/>
                    </linearGradient>
                </defs>
                <rect width="1200" height="{$height}" rx="40" fill="#F8FAFC"/>
                <rect x="40" y="40" width="1120" height="{$cardHeight}" rx="32" fill="#FFFFFF" stroke="#E2E8F0"/>
                <rect x="40" y="40" width="1120" height="160" rx="32" fill="url(#rxGradient)"/>
                <text x="80" y="110" font-family="Inter, Arial, sans-serif" font-size="42" font-weight="800" fill="#FFFFFF">MediQueue Prescription</text>
                <text x="80" y="150" font-family="Inter, Arial, sans-serif" font-size="20" fill="#DBEAFE">{$clinicName}</text>
                <text x="80" y="260" font-family="Inter, Arial, sans-serif" font-size="18" font-weight="700" fill="#2563EB">Patient</text>
                <text x="80" y="295" font-family="Inter, Arial, sans-serif" font-size="30" font-weight="800" fill="#0F172A">{$patientName}</text>
                <text x="720" y="260" font-family="Inter, Arial, sans-serif" font-size="18" font-weight="700" fill="#7C3AED">Doctor</text>
                <text x="720" y="295" font-family="Inter, Arial, sans-serif" font-size="28" font-weight="800" fill="#0F172A">{$doctorName}</text>
                <text x="720" y="325" font-family="Inter, Arial, sans-serif" font-size="16" fill="#64748B">{$specialization}</text>
                <text x="80" y="365" font-family="Inter, Arial, sans-serif" font-size="18" font-weight="700" fill="#22C55E">Diagnosis</text>
                <text x="80" y="395" font-family="Inter, Arial, sans-serif" font-size="20" fill="#0F172A">{$diagnosis}</text>
                {$medicineRows}
                <text x="80" y="{$notesLabelY}" font-family="Inter, Arial, sans-serif" font-size="18" font-weight="700" fill="#2563EB">Notes</text>
                <text x="80" y="{$notesValueY}" font-family="Inter, Arial, sans-serif" font-size="18" fill="#334155">{$notes}</text>
                <text x="80" y="{$issuedAtY}" font-family="Inter, Arial, sans-serif" font-size="16" fill="#64748B">Issued at {$issuedAt}</text>
            </svg>
        SVG;

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="prescription-'.$prescription->id.'.svg"',
        ]);
    }

    private function authorizePrescriptionAccess(Request $request, Prescription $prescription): void
    {
        $user = $request->user();
        $prescription->loadMissing('appointment');

        abort_unless(
            $user->isSuperAdmin()
            || $prescription->patient_id === $user->id
            || ($user->doctorProfile && $prescription->doctor_id === $user->doctorProfile->id)
            || ($user->isAdmin() && $prescription->appointment?->clinic_id === $user->clinic_id),
            403
        );
    }
}
