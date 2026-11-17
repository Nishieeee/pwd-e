<?php

namespace Database\Seeders;

use App\Models\AccessibilityNeed;
use App\Models\Accommodation;
use App\Models\Application;
use App\Models\CandidateEducation;
use App\Models\CandidateProfile;
use App\Models\CandidateWorkExperience;
use App\Models\EducationLevel;
use App\Models\EmployerProfile;
use App\Models\JobAccommodation;
use App\Models\JobMatch;
use App\Models\JobPosting;
use App\Models\LanguageOption;
use App\Models\LocationBarangay;
use App\Models\LocationCity;
use App\Models\SkillOption;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CandidatePortalSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Roles if missing
        Role::findOrCreate('candidate', 'web');
        Role::findOrCreate('employer', 'web');
        Role::findOrCreate('admin', 'web');

        // 1b. Seed Dynamic Location Cities & Barangays via PSGC Seeder
        $this->call(PsgcLocationSeeder::class);

        // 1c. Seed Spoken Language Options
        $languages = ['English', 'Tagalog', 'Chavacano', 'Cebuano', 'FSL'];
        foreach ($languages as $idx => $langName) {
            LanguageOption::updateOrCreate(
                ['name' => $langName],
                ['sort_order' => $idx + 1]
            );
        }

        // 1d. Seed Highest Educational Attainment Options
        $educationLevels = [
            'Primary (Elementary)',
            'Secondary (High School)',
            'Tertiary (College / University)',
            'Vocational / Technical Certification',
        ];
        EducationLevel::whereNotIn('name', $educationLevels)->delete();
        foreach ($educationLevels as $idx => $eduLevel) {
            EducationLevel::updateOrCreate(
                ['name' => $eduLevel],
                ['sort_order' => $idx + 1]
            );
        }

        // 1e. Seed Pre-populated Inclusive & General Skill Options
        $skillsCatalog = [
            ['name' => 'Data Entry', 'category' => 'general'],
            ['name' => 'Customer Service', 'category' => 'general'],
            ['name' => 'Administrative Support', 'category' => 'general'],
            ['name' => 'Cleaning', 'category' => 'general'],
            ['name' => 'Packaging & Sorting', 'category' => 'general'],
            ['name' => 'Bookkeeping', 'category' => 'general'],
            ['name' => 'QA Testing', 'category' => 'general'],
            ['name' => 'Office Tools', 'category' => 'general'],
            ['name' => 'Record Keeping', 'category' => 'general'],
            ['name' => 'Communication', 'category' => 'soft'],
            ['name' => 'Teamwork', 'category' => 'soft'],
            ['name' => 'Inventory Management', 'category' => 'general'],
        ];
        SkillOption::whereNotIn('name', array_column($skillsCatalog, 'name'))->delete();
        foreach ($skillsCatalog as $idx => $skillItem) {
            SkillOption::updateOrCreate(
                ['name' => $skillItem['name']],
                ['category' => $skillItem['category'], 'sort_order' => $idx + 1]
            );
        }

        // 2. Accommodations
        $accommodationsData = [
            'Wheelchair Ramp Access' => [
                'category' => 'mobility',
                'description' => 'Step-free entry and accessible routes.',
            ],
            'Accessible Restroom' => [
                'category' => 'mobility',
                'description' => 'Facilities with space and support rails.',
            ],
            'Adjustable Workstation' => [
                'category' => 'mobility',
                'description' => 'Flexible desk height and seating.',
            ],
            'Screen Reader Compatibility' => [
                'category' => 'visual',
                'description' => 'Digital tools that work with assistive tech.',
            ],
            'Large Print Materials' => [
                'category' => 'visual',
                'description' => 'Larger text for documents and training.',
            ],
            'High-Contrast Displays' => [
                'category' => 'visual',
                'description' => 'Clear contrast on screens and signage.',
            ],
            'FSL Bilingual Staff' => [
                'category' => 'hearing',
                'description' => 'Communicate in Filipino Sign Language.',
            ],
            'Live Captions' => [
                'category' => 'hearing',
                'description' => 'Text captions for meetings and training.',
            ],
            'Visual Alerts' => [
                'category' => 'hearing',
                'description' => 'Visual alternatives to audio notifications.',
            ],
            'Accessible Virtual Interview room' => [
                'category' => 'flexible_work',
                'description' => 'Captioned video room with extra assessment time.',
            ],
            'Screen reader test files' => [
                'category' => 'visual',
                'description' => 'Accessible technical exercise files and instructions.',
            ],
            'Quiet workspace' => [
                'category' => 'speech',
                'description' => 'Low-sensory environment for focused tasks.',
            ],
            'Flexible schedule' => [
                'category' => 'flexible_work',
                'description' => 'Adjustable working hours for therapy or medical visits.',
            ],
            'Remote work option' => [
                'category' => 'flexible_work',
                'description' => 'Work from home or hybrid schedule support.',
            ],
        ];

        $accommodations = [];
        foreach ($accommodationsData as $name => $data) {
            $accommodations[$name] = Accommodation::updateOrCreate(
                ['name' => $name],
                [
                    'category' => $data['category'],
                    'description' => $data['description'] ?? null,
                ]
            );
        }

        // 3. Candidate: Maria Clara Santos
        $candidateUser = User::firstOrCreate(
            ['email' => 'maria.clara.santos@wmsu.edu.ph'],
            [
                'name' => 'Maria Clara Santos',
                'first_name' => 'Maria Clara',
                'last_name' => 'Santos',
                'password' => Hash::make('password'),
                'role' => 'candidate',
                'status' => 'active',
                'verified_at' => Carbon::parse('2026-10-01 08:00:00'),
                'pwd_id_number' => '09-7332-0001842-A',
                'pwd_id_path' => 'documents/pwd_ids/maria_santos_pwd_id.pdf',
                'display_settings' => [
                    'contrast' => 'normal',
                    'font_scale' => 100,
                    'dyslexia_font' => false,
                    'screen_reader' => false,
                ],
            ]
        );
        $candidateUser->assignRole('candidate');

        $zamboangaCity = LocationCity::where('psgc_code', '0931700000')->orWhere('name', 'Zamboanga City')->first();
        $tetuanBarangay = $zamboangaCity
            ? LocationBarangay::where('location_city_id', $zamboangaCity->id)->where('name', 'Tetuan')->first()
            : null;

        $candidateProfile = CandidateProfile::updateOrCreate(
            ['user_id' => $candidateUser->id],
            [
                'headline' => 'Frontend Developer',
                'education_badge' => 'WMSU Alumna',
                'highest_education' => 'Tertiary (College / University)',
                'input_mode' => 'voice',
                'bio' => 'I\'m a WMSU graduate focused on frontend development and digital accessibility. I enjoy creating inclusive web experiences and helping people access information.',
                'location' => 'Zamboanga City, Philippines',
                'country' => 'Philippines',
                'location_city_id' => $zamboangaCity?->id,
                'city' => $zamboangaCity?->name ?? 'Zamboanga City',
                'location_barangay_id' => $tetuanBarangay?->id,
                'barangay' => $tetuanBarangay?->name ?? 'Tetuan',
                'zip_code' => $zamboangaCity?->zip_code ?? '7000',
                'phone' => '+63 917 000 0142',
                'spoken_languages' => ['English', 'Tagalog', 'Chavacano', 'FSL'],
                'skills' => ['Data Entry', 'Customer Service', 'Administrative Support', 'Office Tools', 'Record Keeping', 'Teamwork'],
                'fsl_video_path' => null,
                'resume_pdf_path' => 'resumes/Maria_Santos_Resume.pdf',
                'disability_type' => 'Physical / Motor',
                'visibility_settings' => ['all' => true],
                'accommodation_notes' => 'Please confirm step-free access and offer written instructions before interviews.',
            ]
        );

        CandidateWorkExperience::where('candidate_profile_id', $candidateProfile->id)
            ->where('job_title', '!=', 'Frontend Development Intern')
            ->delete();

        CandidateWorkExperience::updateOrCreate(
            [
                'candidate_profile_id' => $candidateProfile->id,
                'job_title' => 'Frontend Development Intern',
            ],
            [
                'company_name' => 'Inclusive Web Studio',
                'location' => 'Zamboanga City',
                'start_date' => Carbon::parse('2025-01-01'),
                'end_date' => Carbon::parse('2026-01-01'),
                'is_current' => false,
                'description' => 'Built accessible web interfaces with HTML, CSS, JavaScript, and React. Improved keyboard navigation and form labels on a student portal. Tested pages with screen readers.',
            ]
        );

        CandidateEducation::updateOrCreate(
            [
                'candidate_profile_id' => $candidateProfile->id,
                'institution_name' => 'Western Mindanao State University',
            ],
            [
                'degree' => 'Tertiary (College / University)',
                'field_of_study' => 'BS Information Technology',
                'start_date' => Carbon::parse('2021-01-01'),
                'end_date' => Carbon::parse('2025-01-01'),
                'is_current' => false,
            ]
        );

        // Candidate Accommodations (5 specified in Figma)
        $candidateNeeds = [
            'Wheelchair Ramp Access' => ['is_required' => true, 'visible_to_employers' => true],
            'Screen Reader Compatibility' => ['is_required' => true, 'visible_to_employers' => true],
            'FSL Bilingual Staff' => ['is_required' => false, 'visible_to_employers' => true],
            'Accessible Virtual Interview room' => ['is_required' => false, 'visible_to_employers' => true],
            'Screen reader test files' => ['is_required' => false, 'visible_to_employers' => true],
        ];

        foreach ($candidateNeeds as $accName => $needData) {
            if (isset($accommodations[$accName])) {
                AccessibilityNeed::firstOrCreate(
                    [
                        'candidate_profile_id' => $candidateProfile->id,
                        'accommodation_id' => $accommodations[$accName]->id,
                    ],
                    $needData
                );
            }
        }

        // 4. Employers and Job Postings
        $employersData = [
            [
                'email' => 'contact@openpathdigital.ph',
                'name' => 'OpenPath Digital HR',
                'company' => 'OpenPath Digital',
                'location' => 'Remote · Philippines',
                'industry' => 'Digital Studio & Software',
                'jobs' => [
                    [
                        'title' => 'Junior Frontend Developer',
                        'location' => 'Remote · Philippines',
                        'work_arrangement' => 'remote',
                        'employment_type' => 'full_time',
                        'experience_level' => 'Entry level',
                        'salary_min' => 28000,
                        'salary_max' => 35000,
                        'required_skills' => ['React', 'HTML & CSS', 'JavaScript'],
                        'overview_headline' => 'Build a more inclusive web',
                        'description' => 'Help create accessible websites and web applications for Philippine businesses and community organizations. Have energetic support from a robust team, and communities as you grow your frontend skills.',
                        'what_you_will_do' => [
                            'Build responsive web user interfaces using semantic HTML, CSS, and JavaScript.',
                            'Collaborate with designers and developers to test keyboard and screen reader experiences.',
                            'Fix all issues, participate in code reviews and maintain reusable components.',
                        ],
                        'what_you_will_bring' => [
                            'Foundational React, HTML, CSS, and JavaScript skills, with a portfolio/code to share.',
                            'A willingness to learn accessible web practices and collaborate in a remote team.',
                            'Fresh graduates and persons with non-traditional technical projects count as experience.',
                        ],
                        'benefits' => ['Flexible schedule', 'PhilHealth and HMO', 'Equipment stipend', 'Accessibility coaching'],
                        'published_days_ago' => 2,
                        'score' => 98,
                        'matrix' => [
                            ['name' => 'Wheelchair Ramp Access', 'scope' => 'Physical · Main corporate office', 'desc' => 'Step-free entrance to accessible route to all restrooms.'],
                            ['name' => 'Screen Reader Compatibility', 'scope' => 'Digital · Entire workstation', 'desc' => 'All internal tools and daily communication platforms tested for desktop.'],
                            ['name' => 'FSL Bilingual Staff', 'scope' => 'Communication · On-screen & in team', 'desc' => 'Two separate staff available for interviews and team ceremonies.'],
                            ['name' => 'Accessible Virtual Interview room', 'scope' => 'Digital / Accommodated', 'desc' => 'Captioned videoconferencing, extra time for coding questions if FSL require it.'],
                            ['name' => 'Screen reader test files', 'scope' => 'Digital · Assessment phase', 'desc' => 'Accessible tech exercise files, with written instructions in two types.'],
                        ],
                    ],
                ],
            ],
            [
                'email' => 'careers@innovatetech.ph',
                'name' => 'InnovateTech Hiring',
                'company' => 'InnovateTech Philippines Corp.',
                'location' => 'Taguig / Remote',
                'industry' => 'Information Technology',
                'jobs' => [
                    [
                        'title' => 'Digital Accessibility & Frontend Specialist',
                        'location' => 'Taguig / Remote',
                        'work_arrangement' => 'remote',
                        'employment_type' => 'full_time',
                        'experience_level' => 'Entry level',
                        'salary_min' => 30000,
                        'salary_max' => 40000,
                        'required_skills' => ['React', 'HTML & CSS', 'JavaScript', 'Accessibility testing'],
                        'overview_headline' => 'Lead digital accessibility engineering in Manila and beyond',
                        'description' => 'Drive front-end compliance and build beautiful, inclusive interfaces for enterprise web applications.',
                        'what_you_will_do' => [
                            'Audit components for WCAG 2.1 AA compliance.',
                            'Implement accessible frontend patterns with modern web frameworks.',
                            'Pair with QA on screen reader and assistive technology testing.',
                        ],
                        'what_you_will_bring' => [
                            'Understanding of semantic HTML and ARIA.',
                            'Experience with modern frontend toolchains.',
                        ],
                        'benefits' => ['Remote work flexibility', 'HMO coverage with dependents', 'Continuing education budget'],
                        'published_days_ago' => 6,
                        'score' => 98,
                        'matrix' => [
                            ['name' => 'Wheelchair Ramp Access', 'scope' => 'Physical · BGC Office', 'desc' => 'Wheelchair ramp access at BGC office entrance and elevators.'],
                            ['name' => 'Screen Reader Compatibility', 'scope' => 'Digital · Workstation & Tools', 'desc' => 'Full compatibility with NVDA, JAWS, and VoiceOver.'],
                            ['name' => 'Accessible Virtual Interview room', 'scope' => 'Digital · Interview Room', 'desc' => 'Accessible Virtual Room / Google Meet.'],
                            ['name' => 'Screen reader test files', 'scope' => 'Digital · Assessment', 'desc' => 'Screen reader test files provided for tech review.'],
                        ],
                        'application' => [
                            'applied_at' => Carbon::parse('2026-10-25 09:30:00'),
                            'status' => 'interview',
                            'status_subtext' => 'Interview invitation received',
                            'interview_at' => Carbon::parse('2026-11-04 14:00:00'),
                            'interview_location' => 'Accessible Virtual Room · Google Meet',
                            'interview_url' => 'https://meet.google.com/innovatetech-interview',
                            'interview_accommodations' => 'Screen reader test files, wheelchair ramp access at BGC office, quiet interview room.',
                            'updated_at' => Carbon::parse('2026-11-01 11:15:00'),
                        ],
                    ],
                ],
            ],
            [
                'email' => 'hr@zamboangamaritime.com',
                'name' => 'Zamboanga Maritime HR',
                'company' => 'Zamboanga Maritime Logistics Inc.',
                'location' => 'Zamboanga City',
                'industry' => 'Logistics & Supply Chain',
                'jobs' => [
                    [
                        'title' => 'Digital Records & Data Archiving Specialist',
                        'location' => 'Zamboanga City',
                        'work_arrangement' => 'onsite',
                        'employment_type' => 'full_time',
                        'experience_level' => 'Entry level',
                        'salary_min' => 22000,
                        'salary_max' => 28000,
                        'required_skills' => ['Data Archiving', 'Records Management', 'Office Tools'],
                        'overview_headline' => 'Digitize logistics records and maritime documentation',
                        'description' => 'Manage digital filing and documentation workflows for maritime freight operations.',
                        'what_you_will_do' => [
                            'Scan, index, and organize shipping manifests and digital records.',
                            'Ensure database integrity and accessibility of historical documents.',
                        ],
                        'what_you_will_bring' => [
                            'Attention to detail and proficiency in spreadsheet/document tools.',
                        ],
                        'benefits' => ['Government-mandated benefits', 'Maritime transport allowance', 'Yearly performance bonus'],
                        'published_days_ago' => 7,
                        'score' => 92,
                        'matrix' => [
                            ['name' => 'Wheelchair Ramp Access', 'scope' => 'Physical · Main Port Office', 'desc' => 'Step-free access to records department and archive facilities.'],
                            ['name' => 'Screen Reader Compatibility', 'scope' => 'Digital · Archiving System', 'desc' => 'Internal database tested for keyboard and reader navigation.'],
                        ],
                        'application' => [
                            'applied_at' => Carbon::parse('2026-10-27 14:20:00'),
                            'status' => 'under_review',
                            'status_subtext' => 'Employer is reviewing your profile',
                            'updated_at' => Carbon::parse('2026-10-28 16:45:00'),
                        ],
                    ],
                ],
            ],
            [
                'email' => 'contact@accessworks.ph',
                'name' => 'AccessWorks Hiring',
                'company' => 'AccessWorks Studio',
                'location' => 'Hybrid · Zamboanga City',
                'industry' => 'Design & Accessibility',
                'jobs' => [
                    [
                        'title' => 'Web Accessibility Associate',
                        'location' => 'Hybrid · Zamboanga City',
                        'work_arrangement' => 'hybrid',
                        'employment_type' => 'full_time',
                        'experience_level' => 'Entry level',
                        'salary_min' => 25000,
                        'salary_max' => 32000,
                        'required_skills' => ['Accessibility testing', 'Screen readers'],
                        'overview_headline' => 'Elevate accessibility benchmarks across Philippine digital media',
                        'description' => 'Conduct automated and manual assistive technology audits on web and mobile platforms.',
                        'what_you_will_do' => [
                            'Perform accessibility testing using axe-core and NVDA/JAWS.',
                            'Write clear remediation reports for client development teams.',
                        ],
                        'what_you_will_bring' => [
                            'Familiarity with WCAG 2.1 standards and screen reader tools.',
                        ],
                        'benefits' => ['Hybrid schedule', 'Wellness stipend', 'Professional certification support'],
                        'published_days_ago' => 3,
                        'score' => 95,
                        'matrix' => [
                            ['name' => 'Wheelchair Ramp Access', 'scope' => 'Physical · Studio Hub', 'desc' => 'Accessible entry and tactile ground indicators.'],
                            ['name' => 'Screen Reader Compatibility', 'scope' => 'Digital · Studio Testing Lab', 'desc' => 'Screen readers installed across all testing workstations.'],
                            ['name' => 'FSL Bilingual Staff', 'scope' => 'Communication · Studio Team', 'desc' => 'Certified FSL staff on-site.'],
                        ],
                    ],
                ],
            ],
            [
                'email' => 'careers@brightpathtech.ph',
                'name' => 'BrightPath Recruiting',
                'company' => 'BrightPath Business Solutions',
                'location' => 'Remote · Philippines',
                'industry' => 'Software & Consulting',
                'jobs' => [
                    [
                        'title' => 'Junior UI Developer',
                        'location' => 'Remote · Philippines',
                        'work_arrangement' => 'remote',
                        'employment_type' => 'full_time',
                        'experience_level' => 'Entry level',
                        'salary_min' => 26000,
                        'salary_max' => 34000,
                        'required_skills' => ['HTML & CSS', 'React', 'Teamwork'],
                        'overview_headline' => 'Build high-performance web components',
                        'description' => 'Join our product squad delivering modern interfaces for SaaS clients.',
                        'what_you_will_do' => [
                            'Write clean HTML, modern CSS, and React components.',
                            'Ensure responsive layouts across viewports.',
                        ],
                        'what_you_will_bring' => [
                            'Strong grasp of CSS layout models (Flexbox, Grid).',
                        ],
                        'benefits' => ['Internet allowance', 'Flexible hours', 'Health insurance'],
                        'published_days_ago' => 4,
                        'score' => 92,
                        'matrix' => [
                            ['name' => 'Wheelchair Ramp Access', 'scope' => 'Physical · Regional Offices', 'desc' => 'Accessible facilities in regional shared workspaces.'],
                            ['name' => 'Screen Reader Compatibility', 'scope' => 'Digital · Communication Suite', 'desc' => 'Slack and Jira accessibility compliance.'],
                            ['name' => 'FSL Bilingual Staff', 'scope' => 'Communication · Monthly All-Hands', 'desc' => 'Sign language interpretation available for company townhalls.'],
                        ],
                    ],
                    [
                        'title' => 'Content Quality Analyst',
                        'location' => 'Remote',
                        'work_arrangement' => 'remote',
                        'employment_type' => 'full_time',
                        'experience_level' => 'Entry level',
                        'salary_min' => 22000,
                        'salary_max' => 28000,
                        'required_skills' => ['Content Quality', 'HTML & CSS'],
                        'overview_headline' => 'Verify digital content accuracy and formatting',
                        'description' => 'Review web content, ensure styling standards and accessibility descriptions.',
                        'what_you_will_do' => [
                            'Proofread digital articles and check image alt tags.',
                        ],
                        'what_you_will_bring' => [
                            'Great communication skills and eye for formatting.',
                        ],
                        'benefits' => ['Remote work stipend'],
                        'published_days_ago' => 15,
                        'score' => 90,
                        'matrix' => [
                            ['name' => 'Screen Reader Compatibility', 'scope' => 'Digital', 'desc' => 'Supported.'],
                        ],
                        'application' => [
                            'applied_at' => Carbon::parse('2026-10-05 10:15:00'),
                            'status' => 'closed',
                            'status_subtext' => 'Position filled',
                            'updated_at' => Carbon::parse('2026-10-19 15:30:00'),
                        ],
                    ],
                ],
            ],
            [
                'email' => 'jobs@kandolasolutions.ph',
                'name' => 'Kandola Talent',
                'company' => 'Kandola Solutions',
                'location' => 'Remote · Philippines',
                'industry' => 'Digital Marketing & Web',
                'jobs' => [
                    [
                        'title' => 'Web Content Developer',
                        'location' => 'Remote · Philippines',
                        'work_arrangement' => 'remote',
                        'employment_type' => 'full_time',
                        'experience_level' => 'Entry level',
                        'salary_min' => 24000,
                        'salary_max' => 30000,
                        'required_skills' => ['HTML & CSS', 'WordPress'],
                        'overview_headline' => 'Craft engaging web experiences on WordPress',
                        'description' => 'Manage web content publishing, theme styling, and accessible layout configurations.',
                        'what_you_will_do' => [
                            'Publish rich media and maintain WordPress landing pages.',
                        ],
                        'what_you_will_bring' => [
                            'Experience with WordPress CMS and basic HTML/CSS.',
                        ],
                        'benefits' => ['Equipment support', 'Flexible time off'],
                        'published_days_ago' => 5,
                        'score' => 91,
                        'matrix' => [
                            ['name' => 'Wheelchair Ramp Access', 'scope' => 'Physical · HQ', 'desc' => 'Ground floor access.'],
                            ['name' => 'Screen Reader Compatibility', 'scope' => 'Digital · CMS', 'desc' => 'WordPress admin accessible.'],
                            ['name' => 'FSL Bilingual Staff', 'scope' => 'Communication', 'desc' => 'Available upon request.'],
                        ],
                    ],
                ],
            ],
            [
                'email' => 'careers@inclusivestyles.ph',
                'name' => 'Inclusive Styles HR',
                'company' => 'Inclusive Styles PH',
                'location' => 'Hybrid · Zamboanga City',
                'industry' => 'Design & Fashion Tech',
                'jobs' => [
                    [
                        'title' => 'Junior Web QA Tester',
                        'location' => 'Hybrid · Zamboanga City',
                        'work_arrangement' => 'hybrid',
                        'employment_type' => 'full_time',
                        'experience_level' => 'Entry level',
                        'salary_min' => 23000,
                        'salary_max' => 29000,
                        'required_skills' => ['QA Testing', 'Accessibility testing'],
                        'overview_headline' => 'Test inclusive ecommerce experiences',
                        'description' => 'Conduct exploratory and regression tests on ecommerce fashion portals.',
                        'what_you_will_do' => [
                            'Execute test cases across mobile and desktop browsers.',
                        ],
                        'what_you_will_bring' => [
                            'Curiosity and understanding of software QA fundamentals.',
                        ],
                        'benefits' => ['Store discounts', 'HMO health insurance'],
                        'published_days_ago' => 6,
                        'score' => 89,
                        'matrix' => [
                            ['name' => 'Wheelchair Ramp Access', 'scope' => 'Physical · Store & Office', 'desc' => 'Complete ramp and elevator integration.'],
                        ],
                    ],
                ],
            ],
            [
                'email' => 'hr@bayandigital.ph',
                'name' => 'Bayan Digital HR',
                'company' => 'Bayan Digital Services',
                'location' => 'Remote',
                'industry' => 'Business Process & Documentation',
                'jobs' => [
                    [
                        'title' => 'Document Accessibility Associate',
                        'location' => 'Remote',
                        'work_arrangement' => 'remote',
                        'employment_type' => 'full_time',
                        'experience_level' => 'Entry level',
                        'salary_min' => 24000,
                        'salary_max' => 30000,
                        'required_skills' => ['PDF Accessibility', 'Office Suite'],
                        'overview_headline' => 'Make government and corporate documents accessible',
                        'description' => 'Tag, remediate, and structure PDF and Office documents for screen reader compatibility.',
                        'what_you_will_do' => [
                            'Apply PDF/UA tags and verify reading order.',
                        ],
                        'what_you_will_bring' => [
                            'Experience with Adobe Acrobat and document accessibility guidelines.',
                        ],
                        'benefits' => ['100% Remote', 'Paid training'],
                        'published_days_ago' => 20,
                        'score' => 94,
                        'matrix' => [
                            ['name' => 'Screen Reader Compatibility', 'scope' => 'Digital', 'desc' => 'Supported.'],
                        ],
                        'application' => [
                            'applied_at' => Carbon::parse('2026-10-06 11:00:00'),
                            'status' => 'closed',
                            'status_subtext' => 'Offer received · Declined Oct 24',
                            'offer_details' => [
                                'offer_date' => '2026-10-22',
                                'monthly_salary' => 29000,
                                'status' => 'declined',
                            ],
                            'updated_at' => Carbon::parse('2026-10-24 14:00:00'),
                        ],
                    ],
                ],
            ],
            [
                'email' => 'recruitment@aegisfinancial.ph',
                'name' => 'Aegis Talent Acquisition',
                'company' => 'Aegis Financial Partners PH',
                'location' => 'Makati / Hybrid',
                'industry' => 'Financial Services',
                'jobs' => [
                    [
                        'title' => 'Customer Support Specialist',
                        'location' => 'Makati / Hybrid',
                        'work_arrangement' => 'hybrid',
                        'employment_type' => 'full_time',
                        'experience_level' => 'Entry level',
                        'salary_min' => 25000,
                        'salary_max' => 32000,
                        'required_skills' => ['Customer Support', 'Communication'],
                        'overview_headline' => 'Deliver accessible omnichannel customer service',
                        'description' => 'Support banking and insurance customers via email, chat, and phone.',
                        'what_you_will_do' => [
                            'Handle customer inquiries with empathy and high resolution standards.',
                        ],
                        'what_you_will_bring' => [
                            'Clear written and oral communication in English and Filipino.',
                        ],
                        'benefits' => ['Annual profit sharing', 'Comprehensive medical cover'],
                        'published_days_ago' => 14,
                        'score' => 88,
                        'matrix' => [
                            ['name' => 'Wheelchair Ramp Access', 'scope' => 'Physical · Makati Tower', 'desc' => 'Full ADA compliant ramps and automatic doors.'],
                            ['name' => 'Screen Reader Compatibility', 'scope' => 'Digital · CRM', 'desc' => 'Accessible chat console.'],
                        ],
                        'application' => [
                            'applied_at' => Carbon::parse('2026-10-12 16:30:00'),
                            'status' => 'rejected',
                            'status_subtext' => 'Employer moved forward with another candidate',
                            'updated_at' => Carbon::parse('2026-10-22 09:10:00'),
                        ],
                    ],
                ],
            ],
            [
                'email' => 'admin@mindanaocommunity.org',
                'name' => 'Mindanao Community Director',
                'company' => 'Mindanao Community Foundation',
                'location' => 'Zamboanga City',
                'industry' => 'Non-Profit & Community',
                'jobs' => [
                    [
                        'title' => 'Administrative Records Assistant',
                        'location' => 'Zamboanga City',
                        'work_arrangement' => 'onsite',
                        'employment_type' => 'full_time',
                        'experience_level' => 'Entry level',
                        'salary_min' => 20000,
                        'salary_max' => 25000,
                        'required_skills' => ['Record Keeping', 'Office Management'],
                        'overview_headline' => 'Support community outreach programs and records',
                        'description' => 'Maintain registry of community grants and beneficiaries across Region IX.',
                        'what_you_will_do' => [
                            'Catalog community documents and assist visitors.',
                        ],
                        'what_you_will_bring' => [
                            'Commitment to public service and organized record keeping.',
                        ],
                        'benefits' => ['Community impact projects', 'Government leave benefits'],
                        'published_days_ago' => 25,
                        'score' => 85,
                        'matrix' => [
                            ['name' => 'Wheelchair Ramp Access', 'scope' => 'Physical · Community Center', 'desc' => 'Paved ramp to conference and registry rooms.'],
                        ],
                        'application' => [
                            'applied_at' => Carbon::parse('2026-09-28 10:00:00'),
                            'status' => 'closed',
                            'status_subtext' => 'Withdrawn by you · Oct 9',
                            'updated_at' => Carbon::parse('2026-10-09 11:30:00'),
                        ],
                    ],
                ],
            ],
            [
                'email' => 'careers@techforward.ph',
                'name' => 'TechForward HR',
                'company' => 'TechForward Solutions PH',
                'location' => 'Remote · Philippines',
                'industry' => 'Cloud & Enterprise Solutions',
                'jobs' => [
                    [
                        'title' => 'Accessible Software QA Analyst',
                        'location' => 'Remote · Philippines',
                        'work_arrangement' => 'remote',
                        'employment_type' => 'full_time',
                        'experience_level' => 'Entry level',
                        'salary_min' => 26000,
                        'salary_max' => 32000,
                        'required_skills' => ['Accessibility testing', 'QA Testing', 'Teamwork'],
                        'overview_headline' => 'Validate accessible cloud enterprise applications',
                        'description' => 'Test enterprise UI components for WCAG and assistive hardware compatibility.',
                        'what_you_will_do' => [
                            'Execute regression and assistive software verification scripts.',
                        ],
                        'what_you_will_bring' => [
                            'Basic knowledge of software QA methodologies and accessibility.',
                        ],
                        'benefits' => ['Remote work equipment stipend', 'HMO on day 1'],
                        'published_days_ago' => 8,
                        'score' => 87,
                        'matrix' => [
                            ['name' => 'Wheelchair Ramp Access', 'scope' => 'Physical · Partner Hub', 'desc' => 'Step-free access to partner collaboration spaces.'],
                            ['name' => 'Screen Reader Compatibility', 'scope' => 'Digital · Test Tools', 'desc' => 'Internal test suites compatible with screen reader utilities.'],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($employersData as $empData) {
            $empUser = User::firstOrCreate(
                ['email' => $empData['email']],
                [
                    'name' => $empData['name'],
                    'first_name' => explode(' ', $empData['name'])[0],
                    'last_name' => explode(' ', $empData['name'])[1] ?? 'Employer',
                    'password' => Hash::make('password'),
                    'role' => 'employer',
                    'status' => 'active',
                    'verified_at' => Carbon::parse('2026-09-15 08:00:00'),
                ]
            );
            $empUser->assignRole('employer');

            $empProfile = EmployerProfile::firstOrCreate(
                ['user_id' => $empUser->id],
                [
                    'company_name' => $empData['company'],
                    'business_permit_number' => 'BP-2026-'.rand(10000, 99999),
                    'business_permit_path' => 'documents/permits/sample_permit.pdf',
                    'verified_at' => Carbon::parse('2026-09-15 08:00:00'),
                    'industry' => $empData['industry'],
                    'location' => $empData['location'],
                    'workforce_total' => 50,
                    'matrix_completed_at' => Carbon::parse('2026-09-20 10:00:00'),
                ]
            );

            foreach ($empData['jobs'] as $jobData) {
                $posting = JobPosting::firstOrCreate(
                    [
                        'employer_profile_id' => $empProfile->id,
                        'title' => $jobData['title'],
                    ],
                    [
                        'description' => $jobData['description'],
                        'overview_headline' => $jobData['overview_headline'],
                        'required_skills' => $jobData['required_skills'],
                        'location' => $jobData['location'],
                        'work_arrangement' => $jobData['work_arrangement'],
                        'employment_type' => $jobData['employment_type'],
                        'experience_level' => $jobData['experience_level'],
                        'salary_min' => $jobData['salary_min'],
                        'salary_max' => $jobData['salary_max'],
                        'what_you_will_do' => $jobData['what_you_will_do'],
                        'what_you_will_bring' => $jobData['what_you_will_bring'],
                        'benefits' => $jobData['benefits'],
                        'status' => 'published',
                        'published_at' => Carbon::now()->subDays($jobData['published_days_ago']),
                        'scanned_at' => Carbon::now()->subDays($jobData['published_days_ago']),
                    ]
                );

                // Seed Matrix items on JobAccommodations
                if (! empty($jobData['matrix'])) {
                    foreach ($jobData['matrix'] as $mItem) {
                        if (isset($accommodations[$mItem['name']])) {
                            JobAccommodation::firstOrCreate(
                                [
                                    'job_posting_id' => $posting->id,
                                    'accommodation_id' => $accommodations[$mItem['name']]->id,
                                ],
                                [
                                    'scope' => $mItem['scope'],
                                    'description' => $mItem['desc'],
                                    'availability' => '+ Guaranteed',
                                ]
                            );

                            // Also attach to employer_accommodations
                            $empProfile->accommodations()->syncWithoutDetaching([$accommodations[$mItem['name']]->id]);
                        }
                    }
                }

                // Seed JobMatch
                JobMatch::firstOrCreate(
                    [
                        'candidate_profile_id' => $candidateProfile->id,
                        'job_posting_id' => $posting->id,
                    ],
                    [
                        'accessibility_score' => ($jobData['score'] / 100),
                        'skills_score' => 0.95,
                        'total_score' => ($jobData['score'] / 100),
                        'computed_at' => Carbon::now(),
                    ]
                );

                // Seed Application if present
                if (! empty($jobData['application'])) {
                    $appData = $jobData['application'];
                    Application::firstOrCreate(
                        [
                            'candidate_profile_id' => $candidateProfile->id,
                            'job_posting_id' => $posting->id,
                        ],
                        [
                            'status' => $appData['status'],
                            'status_subtext' => $appData['status_subtext'],
                            'applied_at' => $appData['applied_at'],
                            'fit_score_at_apply' => $jobData['score'],
                            'interview_at' => $appData['interview_at'] ?? null,
                            'interview_location' => $appData['interview_location'] ?? null,
                            'interview_url' => $appData['interview_url'] ?? null,
                            'interview_accommodations' => $appData['interview_accommodations'] ?? null,
                            'offer_details' => $appData['offer_details'] ?? null,
                            'updated_at' => $appData['updated_at'] ?? $appData['applied_at'],
                        ]
                    );
                }
            }
        }
    }
}
