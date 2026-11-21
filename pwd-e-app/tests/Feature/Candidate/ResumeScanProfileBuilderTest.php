<?php

namespace Tests\Feature\Candidate;

use App\Livewire\Candidate\ProfileBuilder;
use App\Models\Accommodation;
use App\Models\CandidateProfile;
use App\Models\CandidateWorkExperience;
use App\Models\LocationBarangay;
use App\Models\LocationCity;
use App\Models\SkillOption;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ResumeScanProfileBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['candidate', 'employer', 'admin'] as $role) {
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $this->user = User::factory()->verified()->create([
            'role' => 'candidate',
            'name' => 'Test Candidate',
            'first_name' => 'Test',
            'last_name' => 'Candidate',
            'pwd_id_number' => '12-3456-7890',
        ]);
        $this->user->assignRole('candidate');

        // Seed location & skills
        $city = LocationCity::firstOrCreate(['name' => 'Zamboanga City'], [
            'region' => 'Region IX',
            'province' => 'Zamboanga del Sur',
            'country' => 'Philippines',
            'zip_code' => '7000',
            'psgc_code' => '0931700000',
        ]);
        LocationBarangay::firstOrCreate(['name' => 'Ayala', 'location_city_id' => $city->id], [
            'psgc_code' => '0931700001',
        ]);

        foreach (['HTML', 'CSS', 'JavaScript', 'Data Entry', 'Customer Service'] as $s) {
            SkillOption::firstOrCreate(['name' => $s], ['category' => 'general']);
        }

        Accommodation::firstOrCreate(['name' => 'Wheelchair Ramp Access'], ['category' => 'mobility']);
        Accommodation::firstOrCreate(['name' => 'Screen Reader Compatibility'], ['category' => 'visual']);
    }

    public function test_candidate_can_upload_resume_and_auto_build_profile(): void
    {
        Storage::fake('public');

        $resumeContent = <<<TEXT
Maria Clara Santos
Frontend Developer & Accessibility Specialist
Ayala, Zamboanga City, Philippines
Email: maria.clara.santos@wmsu.edu.ph
Phone: +63 917 000 0142

Professional Summary:
Passionate frontend developer committed to creating accessible, inclusive web experiences for people with disabilities.

Skills:
HTML, CSS, JavaScript, Data Entry, Customer Service

Education:
Tertiary (College / University)
Western Mindanao State University
Class of 2025

Work Experience:
Frontend Development Intern at Inclusive Web Studio (2024 - Present)
Created WCAG AA compliant interfaces and accessible dashboards.

Accommodations:
Wheelchair Ramp Access, Screen Reader Compatibility
TEXT;

        $uploadedFile = UploadedFile::fake()->createWithContent('maria_clara_resume.txt', $resumeContent);

        $this->actingAs($this->user);

        Livewire::test(ProfileBuilder::class)
            ->assertSet('currentStep', 1)
            ->set('resumeScanUpload', $uploadedFile)
            ->assertSet('currentStep', 2)
            ->assertSet('resumeScanSuccess', true)
            ->assertSet('firstName', 'Maria Clara')
            ->assertSet('lastName', 'Santos')
            ->assertSet('phone', '+63 917 000 0142')
            ->assertSet('city', 'Zamboanga City')
            ->assertSet('barangay', 'Ayala')
            ->assertSet('headline', 'Frontend Developer & Accessibility Specialist')
            ->assertSee('Resume Scanned & Profile Auto-Built')
            ->assertSee('Review Mode')
            // Now simulate user reviewing and saving the profile
            ->call('saveProfileAndRedirect')
            ->assertHasNoErrors()
            ->assertSessionHas('profile_saved_banner');

        // Verify the profile was stored in the database
        $profile = CandidateProfile::where('user_id', $this->user->id)->first();
        $this->assertNotNull($profile);
        $this->assertEquals('Zamboanga City', $profile->city);
        $this->assertEquals('Ayala', $profile->barangay);
        $this->assertEquals('+63 917 000 0142', $profile->phone);
        $this->assertContains('HTML', $profile->skills);
        $this->assertContains('JavaScript', $profile->skills);

        // Verify work experience was stored
        $workExp = CandidateWorkExperience::where('candidate_profile_id', $profile->id)->first();
        $this->assertNotNull($workExp);
        $this->assertEquals('Frontend Development Intern', $workExp->job_title);
        $this->assertEquals('Inclusive Web Studio', $workExp->company_name);
        $this->assertTrue($workExp->is_current);

        // Verify resume was stored on disk
        $this->assertNotNull($profile->resume_pdf_path);
        Storage::disk('public')->assertExists($profile->resume_pdf_path);
    }

    public function test_candidate_can_use_sample_resume_review_modal_and_save(): void
    {
        Storage::fake('public');
        $this->actingAs($this->user);

        Livewire::test(ProfileBuilder::class)
            ->call('loadSampleResume')
            ->assertSet('showScanReviewModal', true)
            ->assertSet('resumeScanSuccess', true)
            ->assertSet('firstName', 'Jhon Clein')
            ->assertSet('lastName', 'Pagarogan')
            ->assertSee('Review Scanned Resume Output')
            ->assertSee('Software Engineer & Web Developer')
            ->call('confirmAndSaveFromScan')
            ->assertHasNoErrors()
            ->assertSessionHas('profile_saved_banner');

        $profile = CandidateProfile::where('user_id', $this->user->id)->first();
        $this->assertNotNull($profile);
        $this->assertEquals('Zamboanga City', $profile->city);
        $this->assertEquals('Ayala', $profile->barangay);
        $this->assertContains('HTML', $profile->skills);
        $this->assertContains('JavaScript', $profile->skills);
    }
}
