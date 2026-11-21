<?php

namespace Database\Seeders;

use App\Models\AccessibilityNeed;
use App\Models\Accommodation;
use App\Models\Application;
use App\Models\CandidateProfile;
use App\Models\CandidateWorkExperience;
use App\Models\EmployerProfile;
use App\Models\JobPosting;
use App\Models\LocationBarangay;
use App\Models\LocationCity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CandidateUserSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'candidate', 'guard_name' => 'web']);

        $zamboangaCity = LocationCity::where('name', 'Zamboanga City')->orWhere('psgc_code', '0931700000')->first();
        $ayalaBarangay = $zamboangaCity
            ? LocationBarangay::where('location_city_id', $zamboangaCity->id)->where('name', 'Ayala')->first()
            : null;

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
                'phone' => '+63 917 123 4567',
                'accommodations' => ['Screen reader compatible', 'High-contrast display', 'Large text / zoom', 'Screen Reader Compatibility'],
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
                'phone' => '+63 917 234 5678',
                'accommodations' => ['Wheelchair-accessible entrance', 'Ramp access', 'Accessible restroom', 'Wheelchair Ramp Access'],
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
                'phone' => '+63 917 345 6789',
                'accommodations' => ['Sign language (FSL) interpreter', 'Captioned meetings', 'Live Captions', 'Visual Alerts'],
            ],
            [
                'first_name' => 'Jhon Clein',
                'last_name' => 'Pagarogan',
                'email' => 'pagaroganjhonclein@gmail.com',
                'pwd_id_number' => '25-2312-2132-12',
                'pwd_id_path' => 'documents/pwd_ids/pwd-id-sample.png',
                'disability_type' => 'Motor/Physical',
                'headline' => 'A software engineer specializing in web development and dashboard design',
                'skills' => ['Data Entry', 'HTML', 'CSS', 'JavaScript', 'Excel', 'Word'],
                'location' => 'Zamboanga City, Philippines',
                'city' => 'Zamboanga City',
                'barangay' => 'Ayala',
                'zip_code' => '7000',
                'country' => 'Philippines',
                'location_city_id' => $zamboangaCity?->id,
                'location_barangay_id' => $ayalaBarangay?->id,
                'phone' => '+63 96 9468 5197',
                'date_of_birth' => '2005-10-30',
                'spoken_languages' => ['English', 'Tagalog', 'Chavacano'],
                'highest_education' => 'Tertiary (College / University)',
                'input_mode' => 'voice',
                'accommodations' => ['Wheelchair Ramp Access', 'Accessible Restroom', 'Adjustable Workstation', 'Screen Reader Compatibility'],
                'work_experiences' => [
                    [
                        'job_title' => 'software engineer intern',
                        'company_name' => 'AKA',
                        'location' => 'Zamboanga City',
                        'start_date' => '2024-01-01',
                        'end_date' => null,
                        'is_current' => true,
                        'description' => "Designed and Developed the company's ERP system from scratch using html, css, js",
                    ],
                ],
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

            $profile = CandidateProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'headline' => $data['headline'],
                    'skills' => $data['skills'],
                    'location' => $data['location'],
                    'disability_type' => $data['disability_type'],
                    'phone' => $data['phone'] ?? ('0917'.rand(1000000, 9999999)),
                    'date_of_birth' => $data['date_of_birth'] ?? now()->subYears(rand(22, 35))->toDateString(),
                    'gender' => 'prefer_not_to_say',
                    'city' => $data['city'] ?? null,
                    'barangay' => $data['barangay'] ?? null,
                    'zip_code' => $data['zip_code'] ?? null,
                    'country' => $data['country'] ?? 'Philippines',
                    'location_city_id' => $data['location_city_id'] ?? null,
                    'location_barangay_id' => $data['location_barangay_id'] ?? null,
                    'spoken_languages' => $data['spoken_languages'] ?? ['English', 'Tagalog'],
                    'highest_education' => $data['highest_education'] ?? 'Tertiary (College / University)',
                    'input_mode' => $data['input_mode'] ?? 'manual',
                ]
            );

            // Sync accommodations
            if (!empty($data['accommodations'])) {
                foreach ($data['accommodations'] as $accName) {
                    $acc = Accommodation::where('name', $accName)->first();
                    if ($acc) {
                        AccessibilityNeed::updateOrCreate(
                            [
                                'candidate_profile_id' => $profile->id,
                                'accommodation_id' => $acc->id,
                            ],
                            [
                                'is_required' => true,
                                'visible_to_employers' => true,
                            ]
                        );
                    }
                }
            }

            // Sync work experiences if specified
            if (!empty($data['work_experiences'])) {
                foreach ($data['work_experiences'] as $we) {
                    CandidateWorkExperience::updateOrCreate(
                        [
                            'candidate_profile_id' => $profile->id,
                            'job_title' => $we['job_title'],
                            'company_name' => $we['company_name'],
                        ],
                        [
                            'location' => $we['location'],
                            'start_date' => Carbon::parse($we['start_date']),
                            'end_date' => $we['end_date'] ? Carbon::parse($we['end_date']) : null,
                            'is_current' => $we['is_current'],
                            'description' => $we['description'],
                        ]
                    );
                }
            }
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