<?php

namespace Tests\Unit\Services;

use App\Models\Accommodation;
use App\Models\EducationLevel;
use App\Models\LocationBarangay;
use App\Models\LocationCity;
use App\Models\SkillOption;
use App\Services\Resume\ResumeParserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResumeParserServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ResumeParserService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ResumeParserService();

        // Seed basic location and skills for matching
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

        foreach (['Data Entry', 'Customer Service', 'HTML', 'CSS', 'JavaScript'] as $skill) {
            SkillOption::firstOrCreate(['name' => $skill], ['category' => 'general']);
        }

        Accommodation::firstOrCreate(['name' => 'Wheelchair Ramp Access'], ['category' => 'mobility']);
        Accommodation::firstOrCreate(['name' => 'Screen Reader Compatibility'], ['category' => 'visual']);
    }

    public function test_it_parses_contact_and_personal_details_from_text(): void
    {
        $resumeText = <<<TEXT
Jhon Clein Pagarogan
Software Engineer & Web Developer
Ayala, Zamboanga City, Philippines
Email: pagaroganjhonclein@gmail.com
Phone: +63 96 9468 5197

Professional Summary:
A software engineer specializing in accessible web development, dashboard design, and inclusive user experiences.

Technical Skills:
HTML, CSS, JavaScript, Data Entry, Customer Service

Education:
Tertiary (College / University)
Western Mindanao State University
Bachelor of Science in Computer Science
Class of 2025

Work Experience:
Software Engineer Intern at AKA (2024 - Present)
Designed and developed ERP systems and dashboards with WCAG 2.1 AA accessibility.

Workplace Accommodations Needed:
Wheelchair Ramp Access, Screen Reader Compatibility
TEXT;

        $tempPath = tempnam(sys_get_temp_dir(), 'resume_') . '.txt';
        file_put_contents($tempPath, $resumeText);

        $parsed = $this->service->parse($tempPath, 'jhon_resume.txt');

        @unlink($tempPath);

        $this->assertEquals('Jhon Clein', $parsed['first_name']);
        $this->assertEquals('Pagarogan', $parsed['last_name']);
        $this->assertEquals('pagaroganjhonclein@gmail.com', $parsed['email']);
        $this->assertStringContainsString('468 5197', $parsed['phone']);
        $this->assertEquals('Zamboanga City', $parsed['city']);
        $this->assertEquals('Ayala', $parsed['barangay']);
        $this->assertStringContainsString('Software Engineer', $parsed['headline']);
        $this->assertStringContainsString('accessible web development', $parsed['bio']);
        $this->assertContains('HTML', $parsed['skills']);
        $this->assertContains('JavaScript', $parsed['skills']);
        $this->assertEquals('Western Mindanao State University', $parsed['institution_name']);
        $this->assertEquals('2025', $parsed['year_graduated']);
        $this->assertNotEmpty($parsed['experiences']);
        $this->assertEquals('Software Engineer Intern', $parsed['experiences'][0]['job_title']);
        $this->assertEquals('AKA', $parsed['experiences'][0]['company_name']);
        $this->assertTrue($parsed['experiences'][0]['is_present']);
        $this->assertNotEmpty($parsed['suggested_accommodations']);
    }
}
