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

        $accommodations = Accommodation::take(3)->pluck('id');

        $employers = [
            [
                'email' => 'employer@pwd-e.local',
                'name' => 'Acme Accessibility PH',
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'company_name' => 'Acme Accessibility PH Corp.',
                'business_permit_number' => 'BP-2026-8888',
                'industry' => 'Information Technology & Accessibility',
                'location' => 'Makati City, Metro Manila',
                'jobs' => [
                    ['title' => 'Digital Accessibility Specialist', 'description' => 'Conduct WCAG 2.1 AA audits, test digital assets with screen readers, and partner with engineering teams.', 'required_skills' => ['Accessibility', 'WCAG', 'Screen Readers', 'HTML/CSS'], 'work_arrangement' => 'hybrid', 'employment_type' => 'full_time', 'location' => 'Makati City / Hybrid', 'salary_min' => 45000, 'salary_max' => 65000],
                    ['title' => 'Customer Care & FSL Relay Associate', 'description' => 'Omnichannel customer support with Filipino Sign Language communication assistance.', 'required_skills' => ['Customer Support', 'Filipino Sign Language', 'Chat Support'], 'work_arrangement' => 'remote', 'employment_type' => 'full_time', 'location' => 'Remote', 'salary_min' => 28000, 'salary_max' => 38000],
                    ['title' => 'Remote Data Entry Clerk (PWD-friendly)', 'description' => 'Accurate data entry and record management with flexible hours.', 'required_skills' => ['Data Entry', 'Excel', 'Attention to Detail'], 'work_arrangement' => 'remote', 'employment_type' => 'part_time', 'location' => 'Remote', 'salary_min' => 18000, 'salary_max' => 24000],
                    ['title' => 'Inclusive UX Researcher', 'description' => 'Plan and run usability studies with PWD participants; synthesize inclusive design insights.', 'required_skills' => ['UX Research', 'Accessibility', 'Interviewing'], 'work_arrangement' => 'hybrid', 'employment_type' => 'full_time', 'location' => 'Makati City / Hybrid', 'salary_min' => 50000, 'salary_max' => 70000],
                    ['title' => 'IT Helpdesk Support (Assistive Tech)', 'description' => 'Troubleshoot assistive technologies and provide Tier 1 IT support.', 'required_skills' => ['IT Support', 'Assistive Technology', 'Troubleshooting'], 'work_arrangement' => 'onsite', 'employment_type' => 'full_time', 'location' => 'Makati City', 'salary_min' => 30000, 'salary_max' => 42000],
                    ['title' => 'Administrative Assistant (Flexible Hours)', 'description' => 'Calendar, correspondence, and document management with flexible scheduling.', 'required_skills' => ['Administration', 'Communication', 'MS Office'], 'work_arrangement' => 'hybrid', 'employment_type' => 'full_time', 'location' => 'Makati City / Hybrid', 'salary_min' => 25000, 'salary_max' => 35000],
                    ['title' => 'Social Media & Content Moderator', 'description' => 'Moderate community content and schedule accessible social posts.', 'required_skills' => ['Social Media', 'Content Moderation', 'Copywriting'], 'work_arrangement' => 'remote', 'employment_type' => 'contract', 'location' => 'Remote', 'salary_min' => 22000, 'salary_max' => 32000],
                    ['title' => 'QA Tester (Screen Reader)', 'description' => 'Manual and assistive-tech QA testing of web and mobile apps.', 'required_skills' => ['QA Testing', 'Screen Readers', 'Bug Reporting'], 'work_arrangement' => 'remote', 'employment_type' => 'contract', 'location' => 'Remote', 'salary_min' => 35000, 'salary_max' => 50000],
                    ['title' => 'Community Outreach Coordinator', 'description' => 'Coordinate PWD community programs and employer partnerships.', 'required_skills' => ['Outreach', 'Event Planning', 'Communication'], 'work_arrangement' => 'hybrid', 'employment_type' => 'full_time', 'location' => 'Makati City / Hybrid', 'salary_min' => 32000, 'salary_max' => 45000],
                    ['title' => 'Inventory & Logistics Assistant', 'description' => 'Track inventory and coordinate deliveries with accessible workflows.', 'required_skills' => ['Inventory', 'Logistics', 'Excel'], 'work_arrangement' => 'onsite', 'employment_type' => 'full_time', 'location' => 'Makati City', 'salary_min' => 24000, 'salary_max' => 34000],
                ],
            ],
            [
                'email' => 'employer2@pwd-e.local',
                'name' => 'Inclusive Works Corp.',
                'first_name' => 'Mark',
                'last_name' => 'Reyes',
                'company_name' => 'Inclusive Works Corp.',
                'business_permit_number' => 'BP-2026-9999',
                'industry' => 'Business Process Outsourcing & Inclusion',
                'location' => 'Cebu City, Cebu',
                'jobs' => [
                    ['title' => 'Web Accessibility Auditor', 'description' => 'Audit websites for WCAG compliance and produce remediation reports.', 'required_skills' => ['WCAG', 'Accessibility Audit', 'HTML/CSS'], 'work_arrangement' => 'remote', 'employment_type' => 'contract', 'location' => 'Remote', 'salary_min' => 40000, 'salary_max' => 60000],
                    ['title' => 'Sign Language Interpreter (FSL)', 'description' => 'Provide FSL interpretation for meetings and customer interactions.', 'required_skills' => ['Filipino Sign Language', 'Interpretation'], 'work_arrangement' => 'hybrid', 'employment_type' => 'contract', 'location' => 'Cebu City / Hybrid', 'salary_min' => 30000, 'salary_max' => 45000],
                    ['title' => 'Remote Customer Support Agent', 'description' => 'Handle customer inquiries via chat, email, and voice with flexible shifts.', 'required_skills' => ['Customer Support', 'Chat Support', 'Email'], 'work_arrangement' => 'remote', 'employment_type' => 'full_time', 'location' => 'Remote', 'salary_min' => 26000, 'salary_max' => 36000],
                    ['title' => 'Data Annotator / AI Trainer', 'description' => 'Label datasets and evaluate AI outputs for accuracy.', 'required_skills' => ['Data Annotation', 'Attention to Detail'], 'work_arrangement' => 'remote', 'employment_type' => 'part_time', 'location' => 'Remote', 'salary_min' => 20000, 'salary_max' => 30000],
                    ['title' => 'Technical Writer (Plain Language)', 'description' => 'Write clear, accessible documentation and user guides.', 'required_skills' => ['Technical Writing', 'Plain Language', 'Documentation'], 'work_arrangement' => 'remote', 'employment_type' => 'full_time', 'location' => 'Remote', 'salary_min' => 35000, 'salary_max' => 50000],
                    ['title' => 'HR & Inclusion Coordinator', 'description' => 'Support inclusive hiring, onboarding, and PWD accommodation requests.', 'required_skills' => ['HR', 'Inclusion', 'Communication'], 'work_arrangement' => 'hybrid', 'employment_type' => 'full_time', 'location' => 'Cebu City / Hybrid', 'salary_min' => 32000, 'salary_max' => 46000],
                    ['title' => 'Front Desk Receptionist (Accessible)', 'description' => 'Greet visitors, manage calls, and maintain an accessible front desk.', 'required_skills' => ['Reception', 'Communication', 'Customer Service'], 'work_arrangement' => 'onsite', 'employment_type' => 'full_time', 'location' => 'Cebu City', 'salary_min' => 22000, 'salary_max' => 30000],
                    ['title' => 'Graphic Designer (High Contrast)', 'description' => 'Design accessible marketing materials with high-contrast palettes.', 'required_skills' => ['Graphic Design', 'Accessibility', 'Adobe CC'], 'work_arrangement' => 'hybrid', 'employment_type' => 'contract', 'location' => 'Cebu City / Hybrid', 'salary_min' => 30000, 'salary_max' => 48000],
                    ['title' => 'Operations Assistant (Hybrid)', 'description' => 'Support daily operations, scheduling, and reporting.', 'required_skills' => ['Operations', 'Scheduling', 'Excel'], 'work_arrangement' => 'hybrid', 'employment_type' => 'full_time', 'location' => 'Cebu City / Hybrid', 'salary_min' => 24000, 'salary_max' => 34000],
                    ['title' => 'Virtual Assistant (PWD-friendly)', 'description' => 'Remote admin support with flexible hours and assistive tools.', 'required_skills' => ['Virtual Assistance', 'Administration', 'Communication'], 'work_arrangement' => 'remote', 'employment_type' => 'part_time', 'location' => 'Remote', 'salary_min' => 18000, 'salary_max' => 28000],
                ],
            ],
        ];

        foreach ($employers as $data) {
            $employer = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
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
                    'company_name' => $data['company_name'],
                    'business_permit_number' => $data['business_permit_number'],
                    'industry' => $data['industry'],
                    'location' => $data['location'],
                    'matrix_completed_at' => now(),
                ]
            );

            if ($accommodations->isNotEmpty()) {
                $profile->accommodations()->syncWithoutDetaching($accommodations);
            }

            foreach ($data['jobs'] as $jobData) {
                $job = JobPosting::firstOrCreate(
                    ['employer_profile_id' => $profile->id, 'title' => $jobData['title']],
                    [
                        'description' => $jobData['description'],
                        'required_skills' => $jobData['required_skills'],
                        'work_arrangement' => $jobData['work_arrangement'],
                        'employment_type' => $jobData['employment_type'],
                        'location' => $jobData['location'],
                        'salary_min' => $jobData['salary_min'],
                        'salary_max' => $jobData['salary_max'],
                        'status' => 'published',
                        'scanned_at' => now(),
                        'published_at' => now(),
                    ]
                );

                if ($accommodations->isNotEmpty()) {
                    $job->accommodations()->sync($accommodations);
                }
            }
        }
    }
}
