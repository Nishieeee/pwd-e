<?php

namespace Database\Seeders;

use App\Models\Accommodation;
use App\Models\EmployerProfile;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class EmployerUserSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'employer', 'guard_name' => 'web']);

        $employer = User::firstOrCreate(
            ['email' => 'employer@pwd-e.local'],
            [
                'name' => 'Acme Accessibility PH',
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'password' => Hash::make('password'),
                'role' => 'employer',
                'status' => 'active',
                'verified_at' => now(),
            ]
        );

        $employer->assignRole('employer');

        $profile = EmployerProfile::firstOrCreate(
            ['user_id' => $employer->id],
            [
                'company_name' => 'Acme Accessibility PH Corp.',
                'business_permit_number' => 'BP-2026-8888',
                'industry' => 'Information Technology & Accessibility',
                'location' => 'Makati City, Metro Manila',
                'matrix_completed_at' => now(),
            ]
        );

        // Pre-attach accommodations to employer profile
        $accommodations = Accommodation::take(3)->pluck('id');
        if ($accommodations->isNotEmpty()) {
            $profile->accommodations()->syncWithoutDetaching($accommodations);
        }

        // Seed a sample published job and a sample draft job
        if ($profile->jobPostings()->count() === 0) {
            $job1 = JobPosting::create([
                'employer_profile_id' => $profile->id,
                'title' => 'Digital Accessibility Specialist',
                'description' => 'We are seeking a qualified applicant to conduct WCAG 2.1 AA audits, test digital assets using screen readers, and collaborate with engineering teams.',
                'required_skills' => ['Accessibility', 'WCAG', 'Screen Readers', 'HTML/CSS'],
                'work_arrangement' => 'hybrid',
                'employment_type' => 'full_time',
                'location' => 'Makati City / Hybrid',
                'salary_min' => 45000,
                'salary_max' => 65000,
                'status' => 'published',
                'scanned_at' => now(),
                'published_at' => now(),
            ]);

            if ($accommodations->isNotEmpty()) {
                $job1->accommodations()->sync($accommodations);
            }

            JobPosting::create([
                'employer_profile_id' => $profile->id,
                'title' => 'Customer Care & FSL Relay Associate (Draft)',
                'description' => 'Draft role for omnichannel customer support and Filipino Sign Language communication assistance.',
                'required_skills' => ['Customer Support', 'Filipino Sign Language', 'Chat Support'],
                'work_arrangement' => 'remote',
                'employment_type' => 'full_time',
                'location' => 'Remote',
                'salary_min' => 28000,
                'salary_max' => 38000,
                'status' => 'draft',
            ]);
        }
    }
}
