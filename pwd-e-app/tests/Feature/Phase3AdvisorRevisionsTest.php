<?php

use App\Livewire\Candidate\JobMatches;
use App\Livewire\Candidate\ProfileBuilder;
use App\Livewire\Employer\Applicants;
use App\Models\Accommodation;
use App\Models\Application;
use App\Models\CandidateProfile;
use App\Models\EmployerProfile;
use App\Models\JobMatch;
use App\Models\JobPosting;
use App\Models\SkillOption;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    foreach (['candidate', 'employer', 'admin'] as $role) {
        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
    }
});

test('uploading a resume in profile builder when standard typing is skipped auto-reads skills, experiences, trainings, and tesda certifications (REV-11 & REV-13)', function () {
    Storage::fake('public');

    foreach (['HTML', 'CSS', 'JavaScript', 'Accessibility'] as $idx => $skill) {
        SkillOption::firstOrCreate(['name' => $skill], ['category' => 'technical', 'sort_order' => $idx + 1]);
    }

    $user = User::factory()->create([
        'name' => 'Ana Reyes',
        'first_name' => 'Ana',
        'last_name' => 'Reyes',
        'role' => 'candidate',
        'status' => 'active',
        'verified_at' => now(),
    ]);
    $user->assignRole('candidate');

    $resumeText = implode("\n", [
        'Ana Reyes',
        'Frontend Accessibility Developer',
        'Email: ana.reyes@test.ph',
        'Phone: +63 917 123 4567',
        '',
        'Professional Summary:',
        'Inclusive web developer passionate about WCAG 2.1 AA interfaces and screen-reader usability.',
        '',
        'Skills:',
        'HTML, CSS, JavaScript, Accessibility',
        '',
        'Work Experience:',
        'Frontend Developer Intern at Inclusive Tech PH (2024 - Present)',
        'Built accessible portals and semantic components.',
        '',
        'Trainings & Seminars:',
        '- DICT Digital Jobs Web Accessibility Bootcamp - DICT Region IX (2025)',
        '- Inclusive Workplace Communication Seminar - DOLE (2024)',
        '',
        'Certifications:',
        '- Visual Graphic Design NC III - TESDA (2025)',
        '- Civil Service Professional - Civil Service Commission (2024)',
    ]);

    $fakeResume = UploadedFile::fake()->createWithContent('Ana_Reyes_Resume.txt', $resumeText);

    // Candidate stays in default 'voice' (non-standard-typing) mode and uploads custom resume directly
    $component = Livewire::actingAs($user)
        ->test(ProfileBuilder::class)
        ->set('customResumeUpload', $fakeResume)
        ->assertHasNoErrors();

    expect($component->get('selectedSkills'))->toContain('HTML', 'JavaScript', 'Accessibility')
        ->and($component->get('experiences'))->not->toBeEmpty()
        ->and($component->get('trainings'))->toHaveCount(2)
        ->and($component->get('certificationsList'))->not->toBeEmpty();

    // Saving persists trainings, TESDA certifications, and work experiences to DB
    $component->call('saveDraftAndExit')->assertHasNoErrors();

    $profile = CandidateProfile::where('user_id', $user->id)->firstOrFail();
    expect($profile->trainings()->count())->toBe(2)
        ->and($profile->trainings()->pluck('title')->all())->toContain('DICT Digital Jobs Web Accessibility Bootcamp')
        ->and($profile->certifications()->where('category', 'certification')->count())->toBeGreaterThanOrEqual(2)
        ->and($profile->workExperiences()->count())->toBe(1);

    // Digital profile renders Trainings, Seminars & Professional Certifications section
    $this->actingAs($user)
        ->get(route('candidate.profile.show'))
        ->assertOk()
        ->assertSee('Trainings, Seminars & Professional Certifications')
        ->assertSee('DICT Digital Jobs Web Accessibility Bootcamp')
        ->assertSee('Visual Graphic Design NC III');
});

test('candidate job matches and employer applicants render expandable match breakdown and filter by match score (REV-12)', function () {
    $acc = Accommodation::firstOrCreate(
        ['name' => 'Screen Reader Compatibility'],
        ['category' => 'visual', 'description' => 'NVDA/JAWS tested workspace']
    );

    $employerUser = User::factory()->create([
        'role' => 'employer',
        'status' => 'active',
        'verified_at' => now(),
    ]);
    $employerUser->assignRole('employer');
    $employerProfile = EmployerProfile::create([
        'user_id' => $employerUser->id,
        'company_name' => 'OpenPath Inclusive Corp',
        'business_permit_number' => 'BP-2026-555',
    ]);

    $job = JobPosting::create([
        'employer_profile_id' => $employerProfile->id,
        'title' => 'Accessibility QA Specialist',
        'description' => 'Audit web apps for WCAG compliance.',
        'employment_type' => 'full_time',
        'work_arrangement' => 'remote',
        'location' => 'Zamboanga City',
        'salary_min' => 25000,
        'salary_max' => 35000,
        'required_skills' => ['HTML', 'Accessibility', 'QA Testing'],
        'status' => 'published',
        'published_at' => now(),
    ]);
    $job->accommodations()->sync([$acc->id]);

    $candTopUser = User::factory()->create(['name' => 'Top Match Candidate', 'role' => 'candidate', 'status' => 'active', 'verified_at' => now()]);
    $candTopUser->assignRole('candidate');
    $candTopProfile = CandidateProfile::create([
        'user_id' => $candTopUser->id,
        'skills' => ['HTML', 'Accessibility', 'QA Testing'],
        'highest_education' => 'Tertiary (College / University)',
    ]);
    $candTopProfile->accessibilityNeeds()->create(['accommodation_id' => $acc->id, 'is_required' => true, 'visible_to_employers' => true]);
    $candTopProfile->trainings()->create(['title' => 'WCAG QA Workshop', 'organizer' => 'DICT', 'year_completed' => '2025']);
    JobMatch::create([
        'candidate_profile_id' => $candTopProfile->id,
        'job_posting_id' => $job->id,
        'accessibility_score' => 1.0,
        'skills_score' => 0.92,
        'total_score' => 0.95,
        'computed_at' => now(),
    ]);

    $candLowUser = User::factory()->create(['name' => 'Lower Match Candidate', 'role' => 'candidate', 'status' => 'active', 'verified_at' => now()]);
    $candLowUser->assignRole('candidate');
    $candLowProfile = CandidateProfile::create([
        'user_id' => $candLowUser->id,
        'skills' => ['Data Entry'],
    ]);

    $appTop = Application::create([
        'candidate_profile_id' => $candTopProfile->id,
        'job_posting_id' => $job->id,
        'status' => 'applied',
        'fit_score_at_apply' => 95.0,
        'applied_at' => now(),
    ]);

    $appLow = Application::create([
        'candidate_profile_id' => $candLowProfile->id,
        'job_posting_id' => $job->id,
        'status' => 'applied',
        'fit_score_at_apply' => 68.0,
        'applied_at' => now(),
    ]);

    // 1. Candidate JobMatches shows Overall Match % + View Match Breakdown drawer with Accessibility & Skills/Achievements pillars
    Livewire::actingAs($candTopUser)
        ->test(JobMatches::class, ['job' => $job->id])
        ->assertSee('95% Overall Match')
        ->assertSee('View Match Breakdown')
        ->assertSee('Accessibility & Accommodations Fit')
        ->assertSee('Skills & Core Competencies (Résumé)')
        ->assertSee('Experience, Trainings & Seminars')
        ->assertSee('Certifications, TESDA & Education');

    // 2. Employer Applicants filters by Match Score and displays View Match Breakdown inside applicant modal
    Livewire::actingAs($employerUser)
        ->test(Applicants::class)
        ->assertSee('Top Match Candidate')
        ->assertSee('Lower Match Candidate')
        ->set('matchScoreFilter', '90')
        ->assertSee('Top Match Candidate')
        ->assertDontSee('Lower Match Candidate')
        ->set('matchScoreFilter', 'below_70')
        ->assertSee('Lower Match Candidate')
        ->assertDontSee('Top Match Candidate')
        ->set('matchScoreFilter', 'all')
        ->call('viewDetails', $appTop->id)
        ->assertSee('View Match Breakdown')
        ->assertSee('WCAG QA Workshop');
});
