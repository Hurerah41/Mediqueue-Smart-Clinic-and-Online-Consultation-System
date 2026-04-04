<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Specialization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AiAssistantController extends Controller
{
    public function symptomCheck(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'symptoms' => ['required', 'string', 'max:1000'],
        ]);

        $text = Str::lower($validated['symptoms']);

        $rules = [
            'Cardiology' => ['chest', 'heart', 'bp', 'palpitation'],
            'Dermatology' => ['skin', 'rash', 'acne', 'itch'],
            'Pediatrics' => ['child', 'baby', 'infant', 'fever'],
            'Orthopedics' => ['bone', 'joint', 'fracture', 'knee', 'back'],
            'General Medicine' => ['fever', 'flu', 'cough', 'pain', 'headache'],
        ];

        $specializationName = 'General Medicine';

        foreach ($rules as $name => $keywords) {
            if (Str::contains($text, $keywords)) {
                $specializationName = $name;
                break;
            }
        }

        $specialization = Specialization::where('name', $specializationName)->first()
            ?? Specialization::first();

        $doctors = Doctor::query()
            ->with(['user', 'clinic.area', 'specialization'])
            ->when($specialization, fn ($query) => $query->where('specialization_id', $specialization->id))
            ->take(5)
            ->get()
            ->map(fn (Doctor $doctor) => [
                'doctor' => $doctor->user->name,
                'specialization' => $doctor->specialization->name,
                'clinic' => $doctor->clinic->name,
                'area' => $doctor->clinic->area->name,
            ]);

        return response()->json([
            'suggested_specialization' => $specialization?->name ?? 'General Medicine',
            'message' => 'Based on your symptoms, this specialization may be a good starting point. Please consult a qualified doctor for medical advice.',
            'doctors' => $doctors,
        ]);
    }

    public function chatbot(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $message = Str::lower($validated['message']);

        $reply = match (true) {
            Str::contains($message, ['token', 'queue', 'wait']) => 'Open your appointment page to see your live token status and estimated wait time.',
            Str::contains($message, ['online', 'video', 'consultation']) => 'Choose Online Consultation while booking. Your appointment page will show the secure video room link and chat panel.',
            Str::contains($message, ['prescription', 'medicine']) => 'Once your doctor saves a prescription, you can view it from the appointment details page.',
            Str::contains($message, ['clinic', 'area', 'doctor']) => 'Use the Clinics page to filter by area and pick a doctor based on specialization and online availability.',
            default => 'I can help with booking, queue tracking, online consultation, and prescriptions. Please describe what you need in one short sentence.',
        };

        return response()->json(['reply' => $reply]);
    }
}
