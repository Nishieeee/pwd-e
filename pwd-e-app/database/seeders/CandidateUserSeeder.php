<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\CandidateProfile;
use App\Models\EmployerProfile;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CandidateUserSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'candidate', 'guard_name' => 'web']);

        $candidates = [
            [
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'email' => 'candidate1@pwd-e.local',
                'pwd_id_number' => '13-3901-0003421-V',
                'pwd_id_path' => 'documents/pwd_ids/pwd-id-sample.png',
                'disability_type' => 'Visual',
                'headline' => 'Accessibility Tester',
                'skills' => ['WCAG', 'Screen Readers', 'HTML/CSS'],
                'location' => 'Quezon City',
            ],
            [
                'first_name' => 'Jose',
                'last_name' => 'Reyes',
                'email' => 'candidate2@pwd-e.local',
                'pwd_id_number' => '07-2217-0005912-M',
                'pwd_id_path' => 'documents/pwd_ids/pwd-id-sample.png',
                'disability_type' => 'Mobility',
                'headline' => 'Customer Support Specialist',
                'skills' => ['Customer Support', 'Chat Support', 'Data Entry'],
                'location' => 'Cebu City',
            ],
            [
                'first_name' => 'Ana',
                'last_name' => 'Cruz',
                'email' => 'candidate3@pwd-e.local',
                'pwd_id_number' => '11-7604-0008123-H',
                'pwd_id_path' => 'documents/pwd_ids/pwd-id-sample.png',
                'disability_type' => 'Hearing',
                'headline' => 'FSL Interpreter',
                'skills' => ['Filipino Sign Language', 'Communication', 'Training'],
                'location' => 'Davao City',
            ],
        ];

        foreach ($candidates as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['first_name'].' '.$data['last_name'],
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'password' => Hash::make('password'),
                    'role' => 'candidate',
                    'status' => 'active',
                    'verified_at' => now(),
                    'pwd_id_number' => $data['pwd_id_number'],
                    'pwd_id_path' => $data['pwd_id_path'],
                ]
            );
            $user->update([
                'pwd_id_number' => $data['pwd_id_number'],
                'pwd_id_path' => $data['pwd_id_path'],
            ]);

            $user->assignRole('candidate');

            CandidateProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'headline' => $data['headline'],
                    'skills' => $data['skills'],
                    'location' => $data['location'],
                    'disability_type' => $data['disability_type'],
                    'phone' => '0917'.rand(1000000, 9999999),
                    'date_of_birth' => now()->subYears(rand(22, 35))->toDateString(),
                    'gender' => 'prefer_not_to_say',
                ]
            );
        }

        // Seed a sample application from candidate1 to the published job
        $employer = User::where('email', 'employer@pwd-e.local')->first();
        $job = $employer
            ? JobPosting::where('employer_profile_id', $employer->employerProfile?->id)
                ->where('status', 'published')->first()
            : null;

        $candidate1 = User::where('email', 'candidate1@pwd-e.local')->first();

        if ($job && $candidate1 && $candidate1->candidateProfile) {
            Application::firstOrCreate(
                [
                    'candidate_profile_id' => $candidate1->candidateProfile->id,
                    'job_posting_id' => $job->id,
                ],
                [
                    'status' => 'applied',
                    'fit_score_at_apply' => 82.50,
                    'applied_at' => now()->subDays(2),
                ]
            );
        }
    }
}