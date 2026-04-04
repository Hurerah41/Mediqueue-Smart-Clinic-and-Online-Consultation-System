<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\PlatformSetting;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $areas = collect(['Gulshan', 'PECHS', 'Liaquatabad'])->mapWithKeys(
            fn (string $name) => [$name => Area::firstOrCreate(['name' => $name], ['city' => 'Karachi'])]
        );

        $specializations = collect([
            'General Medicine',
            'Cardiology',
            'Dermatology',
            'Pediatrics',
            'Orthopedics',
        ])->mapWithKeys(
            fn (string $name) => [$name => Specialization::firstOrCreate(['name' => $name], ['description' => $name.' specialist care'])]
        );

        $clinic = Clinic::firstOrCreate(
            ['slug' => 'gulshan-smart-care'],
            [
                'area_id' => $areas['Gulshan']->id,
                'name' => 'Gulshan Smart Care Clinic',
                'phone' => '+92-300-1111111',
                'address' => 'Block 13, Gulshan-e-Iqbal, Karachi',
                'opens_at' => '09:00:00',
                'closes_at' => '22:00:00',
                'is_active' => true,
            ]
        );


        User::updateOrCreate(
            ['email' => 'owner@mediqueue.test'],
            [
                'clinic_id' => null,
                'name' => 'MediQueue Owner',
                'phone' => '+92-300-9999999',
                'role' => User::ROLE_SUPER_ADMIN,
                'password' => 'password',
                'is_verified' => true,
                'email_verified_at' => now(),
            ]
        );

        Clinic::firstOrCreate(
            ['slug' => 'pechs-health-hub'],
            [
                'area_id' => $areas['PECHS']->id,
                'name' => 'PECHS Health Hub',
                'phone' => '+92-300-2222222',
                'address' => 'Main Shahrah-e-Faisal, PECHS, Karachi',
                'opens_at' => '10:00:00',
                'closes_at' => '21:00:00',
                'is_active' => true,
            ]
        );

        Clinic::firstOrCreate(
            ['slug' => 'liaquatabad-family-clinic'],
            [
                'area_id' => $areas['Liaquatabad']->id,
                'name' => 'Liaquatabad Family Clinic',
                'phone' => '+92-300-3333333',
                'address' => 'C-1 Area, Liaquatabad, Karachi',
                'opens_at' => '08:30:00',
                'closes_at' => '20:00:00',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@smartclinic.test'],
            [
                'clinic_id' => $clinic->id,
                'name' => 'Clinic Admin',
                'phone' => '+92-300-1000001',
                'role' => User::ROLE_ADMIN,
                'password' => 'password',
                'is_verified' => true,
                'email_verified_at' => now(),
            ]
        );

        $doctorUser = User::updateOrCreate(
            ['email' => 'doctor@smartclinic.test'],
            [
                'clinic_id' => $clinic->id,
                'name' => 'Ayesha Khan',
                'phone' => '+92-300-1000002',
                'role' => User::ROLE_DOCTOR,
                'password' => 'password',
                'is_verified' => true,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'patient@smartclinic.test'],
            [
                'clinic_id' => null,
                'name' => 'Ali Raza',
                'phone' => '+92-300-1000003',
                'role' => User::ROLE_PATIENT,
                'password' => 'password',
                'is_verified' => true,
                'email_verified_at' => now(),
            ]
        );

        $doctor = Doctor::updateOrCreate(
            ['user_id' => $doctorUser->id],
            [
                'clinic_id' => $clinic->id,
                'specialization_id' => $specializations['General Medicine']->id,
                'license_no' => 'LIC-GEN-001',
                'experience_years' => 8,
                'consultation_fee' => 1500,
                'offers_online_consultation' => true,
                'avg_consultation_minutes' => 15,
                'bio' => 'Family medicine doctor focused on preventive care, fever, flu, and primary diagnosis.',
            ]
        );

        foreach (range(1, 6) as $weekday) {
            DoctorSchedule::updateOrCreate(
                ['doctor_id' => $doctor->id, 'weekday' => $weekday],
                [
                    'starts_at' => '10:00:00',
                    'ends_at' => '18:00:00',
                    'slot_limit' => 30,
                    'is_active' => true,
                ]
            );
        }

        PlatformSetting::updateOrCreate(
            ['id' => 1],
            [
                'platform_name' => 'MediQueue',
                'support_email' => 'support@mediqueue.test',
                'commission_percent' => 10,
                'queue_alert_threshold' => 2,
                'clinic_verification_policy' => 'Verify clinic license, physical address, admin contact, medical services, and subscription plan before approval.',
                'owner_notes' => 'Owner settings control platform policy, commission, and queue alert behavior.',
            ]
        );
    }
}
