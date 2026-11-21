<?php

namespace App\Services\Resume;

use App\Models\Accommodation;
use App\Models\EducationLevel;
use App\Models\LocationBarangay;
use App\Models\LocationCity;
use App\Models\SkillOption;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser as PdfParser;
use Throwable;
use ZipArchive;

class ResumeParserService
{
    /**
     * Parse an uploaded resume or file path into structured candidate profile data.
     *
     * @param  string|UploadedFile  $file
     * @param  string|null  $originalFilename
     * @return array
     */
    public function parse(string|UploadedFile $file, ?string $originalFilename = null): array
    {
        $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;
        $filename = $originalFilename ?? ($file instanceof UploadedFile ? $file->getClientOriginalName() : basename($filePath));
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $rawText = $this->extractText($filePath, $extension);

        return $this->extractProfileData($rawText, $filename);
    }

    /**
     * Extract plain text content from PDF, DOCX, or text files.
     */
    public function extractText(string $filePath, string $extension): string
    {
        if (! file_exists($filePath)) {
            return '';
        }

        return match ($extension) {
            'pdf' => $this->extractFromPdf($filePath),
            'docx' => $this->extractFromDocx($filePath),
            'txt', 'text', 'rtf', 'md' => @file_get_contents($filePath) ?: '',
            default => @file_get_contents($filePath) ?: '',
        };
    }

    protected function extractFromPdf(string $filePath): string
    {
        try {
            $parser = new PdfParser();
            $pdf = $parser->parseFile($filePath);
            $text = $pdf->getText();

            if (! empty(trim($text))) {
                return $text;
            }
        } catch (Throwable) {
            // Fallback to raw stream extraction if Smalot encounters uncompressed streams
        }

        $content = @file_get_contents($filePath) ?: '';
        if (preg_match_all('/\((.*?)\)\s*Tj/s', $content, $matches)) {
            return implode(' ', $matches[1]);
        }

        return '';
    }

    protected function extractFromDocx(string $filePath): string
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) === true) {
            $xml = $zip->getFromName('word/document.xml');
            $zip->close();
            if ($xml) {
                // Preserve paragraph and row breaks
                $xml = str_replace(['</w:p>', '</w:tr>'], "\n", $xml);

                return html_entity_decode(strip_tags($xml), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }

        return '';
    }

    /**
     * Analyze extracted resume text and convert it to structured profile fields.
     */
    public function extractProfileData(string $text, string $filename = ''): array
    {
        $cleanText = str_replace(["\r\n", "\r"], "\n", $text);
        $lines = array_values(array_filter(array_map('trim', explode("\n", $cleanText))));

        $email = $this->extractEmail($cleanText);
        $phone = $this->extractPhone($cleanText);
        $nameParts = $this->extractName($lines, $email);
        $location = $this->extractLocation($cleanText);
        $headline = $this->extractHeadline($lines, $cleanText);
        $bio = $this->extractBio($lines, $cleanText);
        $skills = $this->extractSkills($cleanText);
        $education = $this->extractEducation($lines, $cleanText);
        $experiences = $this->extractExperiences($lines, $cleanText);
        $languages = $this->extractLanguages($cleanText);
        $accommodations = $this->extractAccommodations($cleanText);

        return [
            'filename' => $filename,
            'first_name' => $nameParts['first_name'] ?? '',
            'middle_name' => $nameParts['middle_name'] ?? '',
            'last_name' => $nameParts['last_name'] ?? '',
            'full_name' => $nameParts['full_name'] ?? '',
            'email' => $email,
            'phone' => $phone,
            'country' => $location['country'] ?? 'Philippines',
            'city' => $location['city'] ?? '',
            'barangay' => $location['barangay'] ?? '',
            'location_city_id' => $location['location_city_id'] ?? null,
            'location_barangay_id' => $location['location_barangay_id'] ?? null,
            'zip_code' => $location['zip_code'] ?? '',
            'headline' => $headline,
            'bio' => $bio,
            'skills' => $skills,
            'highest_education' => $education['highest_education'] ?? 'Tertiary (College / University)',
            'institution_name' => $education['institution_name'] ?? '',
            'year_graduated' => $education['year_graduated'] ?? '',
            'degree' => $education['degree'] ?? '',
            'experiences' => $experiences,
            'spoken_languages' => $languages,
            'suggested_accommodations' => $accommodations,
            'raw_text_length' => strlen($cleanText),
        ];
    }

    protected function extractEmail(string $text): string
    {
        if (preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $text, $matches)) {
            return trim($matches[0]);
        }

        return '';
    }

    protected function extractPhone(string $text): string
    {
        if (preg_match('/(?:\+?63|0)[\s.-]?(?:9[\d\s.-]{7,14}\d)/', $text, $matches)) {
            $digits = preg_replace('/\D/', '', $matches[0]);
            if (str_starts_with($digits, '63')) {
                $digits = substr($digits, 2);
            } elseif (str_starts_with($digits, '0')) {
                $digits = substr($digits, 1);
            }
            if (strlen($digits) === 10) {
                return '+63 ' . substr($digits, 0, 3) . ' ' . substr($digits, 3, 3) . ' ' . substr($digits, 6, 4);
            }

            return trim($matches[0]);
        }

        return '';
    }

    protected function extractName(array $lines, string $email): array
    {
        $ignoreWords = ['resume', 'curriculum', 'vitae', 'cv', 'profile', 'summary', 'contact', 'education', 'skills', 'experience', 'email', 'phone'];

        foreach (array_slice($lines, 0, 8) as $line) {
            $line = trim($line);
            if (empty($line) || strlen($line) > 50) {
                continue;
            }
            if (str_contains($line, '@') || preg_match('/\d{4}/', $line)) {
                continue;
            }
            if (in_array(strtolower($line), $ignoreWords, true)) {
                continue;
            }

            // Looks like a name (2 to 4 capitalized words)
            $words = preg_split('/\s+/', $line);
            if (count($words) >= 2 && count($words) <= 4) {
                $allAlpha = true;
                foreach ($words as $w) {
                    if (! preg_match('/^[A-Z][a-zA-Z\.\'-]*$/', $w)) {
                        $allAlpha = false;
                        break;
                    }
                }
                if ($allAlpha) {
                    if (count($words) === 2) {
                        $firstName = $words[0];
                        $middleName = '';
                        $lastName = $words[1];
                    } elseif (count($words) === 3) {
                        if (strlen($words[1]) <= 2 || str_ends_with($words[1], '.')) {
                            $firstName = $words[0];
                            $middleName = rtrim($words[1], '.');
                            $lastName = $words[2];
                        } else {
                            $firstName = $words[0] . ' ' . $words[1];
                            $middleName = '';
                            $lastName = $words[2];
                        }
                    } else {
                        $firstName = $words[0] . ' ' . $words[1];
                        $middleName = $words[2];
                        $lastName = $words[3];
                    }

                    return [
                        'first_name' => $firstName,
                        'middle_name' => $middleName,
                        'last_name' => $lastName,
                        'full_name' => $line,
                    ];
                }
            }
        }

        // Fallback from email prefix if clean
        if ($email) {
            $prefix = explode('@', $email)[0];
            $parts = preg_split('/[\._-]/', $prefix);
            if (count($parts) >= 2) {
                return [
                    'first_name' => ucfirst($parts[0]),
                    'middle_name' => '',
                    'last_name' => ucfirst(end($parts)),
                    'full_name' => ucfirst($parts[0]).' '.ucfirst(end($parts)),
                ];
            }
        }

        return ['first_name' => '', 'middle_name' => '', 'last_name' => '', 'full_name' => ''];
    }

    protected function extractLocation(string $text): array
    {
        $result = [
            'country' => 'Philippines',
            'city' => '',
            'barangay' => '',
            'location_city_id' => null,
            'location_barangay_id' => null,
            'zip_code' => '',
        ];

        // Search through known Philippine Cities
        $cities = LocationCity::all();
        foreach ($cities as $c) {
            if (stripos($text, $c->name) !== false) {
                $result['city'] = $c->name;
                $result['location_city_id'] = $c->id;
                $result['zip_code'] = $c->zip_code ?: '';

                // Search for barangays belonging to this city
                $barangays = $c->barangays;
                foreach ($barangays as $b) {
                    if (stripos($text, $b->name) !== false) {
                        $result['barangay'] = $b->name;
                        $result['location_barangay_id'] = $b->id;
                        break;
                    }
                }
                break;
            }
        }

        // Common defaults if not found in db
        if (empty($result['city'])) {
            $commonCities = ['Zamboanga City', 'Quezon City', 'Manila', 'Cebu City', 'Davao City', 'Makati City', 'Taguig', 'Pasig'];
            foreach ($commonCities as $cityName) {
                if (stripos($text, $cityName) !== false) {
                    $result['city'] = $cityName;
                    break;
                }
            }
        }

        return $result;
    }

    protected function extractHeadline(array $lines, string $text): string
    {
        $commonHeadlines = [
            'Software Engineer',
            'Frontend Developer',
            'Backend Developer',
            'Full Stack Developer',
            'Web Developer',
            'Accessibility Tester',
            'Accessibility Specialist',
            'Customer Support Specialist',
            'Customer Service Representative',
            'Data Entry Specialist',
            'Data Entry Clerk',
            'Virtual Assistant',
            'FSL Interpreter',
            'Quality Assurance Tester',
            'QA Engineer',
            'Administrative Assistant',
            'IT Support Specialist',
        ];

        foreach ($commonHeadlines as $h) {
            if (stripos($text, $h) !== false) {
                // If headline is followed by specialization in text, capture up to 80 chars
                if (preg_match('/'.preg_quote($h, '/').'[^\n,\.]+/i', $text, $m)) {
                    $val = trim($m[0]);
                    if (strlen($val) <= 90) {
                        return $val;
                    }
                }

                return $h;
            }
        }

        // Look at lines 2 to 5 for a concise title
        foreach (array_slice($lines, 1, 4) as $line) {
            if (strlen($line) >= 8 && strlen($line) <= 70 && ! str_contains($line, '@') && ! preg_match('/\d{5}/', $line)) {
                return $line;
            }
        }

        return 'PWD-E Candidate Professional';
    }

    protected function extractBio(array $lines, string $text): string
    {
        $summaryHeaders = ['summary', 'professional summary', 'career objective', 'about me', 'profile', 'executive summary'];
        $foundSection = false;
        $bioLines = [];

        foreach ($lines as $line) {
            $clean = strtolower(trim($line, " \t\n\r\0\x0B:"));
            if (in_array($clean, $summaryHeaders, true)) {
                $foundSection = true;
                continue;
            }

            if ($foundSection) {
                // Stop at next major section
                if (preg_match('/^(experience|work experience|education|skills|certifications|projects|awards)/i', $line)) {
                    break;
                }
                if (! empty($line)) {
                    $bioLines[] = $line;
                    if (count($bioLines) >= 4) {
                        break;
                    }
                }
            }
        }

        if (! empty($bioLines)) {
            return implode(' ', $bioLines);
        }

        // Fallback: look for a descriptive paragraph in the first 15 lines
        foreach (array_slice($lines, 2, 10) as $line) {
            if (strlen($line) > 60 && ! str_contains($line, '@') && ! str_contains($line, 'http')) {
                return $line;
            }
        }

        return '';
    }

    protected function extractSkills(string $text): array
    {
        $detected = [];

        // 1. Check database SkillOptions
        $catalogSkills = SkillOption::pluck('name')->toArray();
        if (empty($catalogSkills)) {
            $catalogSkills = [
                'Data Entry', 'Customer Service', 'Administrative Support', 'Cleaning',
                'Packaging & Sorting', 'Bookkeeping', 'QA Testing', 'Office Tools',
                'Record Keeping', 'Communication', 'Teamwork', 'Inventory Management',
            ];
        }

        // Expanded technology and workplace skills
        $additionalSkills = [
            'HTML', 'CSS', 'JavaScript', 'React', 'PHP', 'Laravel', 'Python', 'Java',
            'SQL', 'Excel', 'Word', 'PowerPoint', 'Google Docs', 'Google Sheets',
            'WCAG', 'Screen Readers', 'Assistive Technology', 'Filipino Sign Language', 'FSL',
            'Chat Support', 'Technical Writing', 'Graphic Design', 'Troubleshooting',
        ];

        $allSkills = array_unique(array_merge($catalogSkills, $additionalSkills));

        foreach ($allSkills as $skill) {
            // Match whole word
            if (preg_match('/\b' . preg_quote($skill, '/') . '\b/i', $text)) {
                $detected[] = $skill;
            }
        }

        // Also parse comma-separated lists under "Skills" header
        if (preg_match('/(?:skills|technical skills|key competencies)[:\s]+([^\n]+(?:\n[^\n]+)?)/i', $text, $matches)) {
            $rawList = $matches[1];
            $items = preg_split('/[,•|·\n\t]+/', $rawList);
            foreach ($items as $item) {
                $clean = trim($item);
                if (strlen($clean) >= 2 && strlen($clean) <= 30 && ! in_array(strtolower($clean), ['and', 'etc', 'skills', 'experience'], true)) {
                    $detected[] = ucwords(strtolower($clean));
                }
            }
        }

        return array_values(array_unique($detected));
    }

    protected function extractEducation(array $lines, string $text): array
    {
        $result = [
            'highest_education' => 'Tertiary (College / University)',
            'institution_name' => '',
            'year_graduated' => '',
            'degree' => '',
        ];

        // Detect education level
        if (stripos($text, 'college') !== false || stripos($text, 'university') !== false || stripos($text, 'bachelor') !== false || stripos($text, 'bs ') !== false) {
            $result['highest_education'] = 'Tertiary (College / University)';
        } elseif (stripos($text, 'vocational') !== false || stripos($text, 'tesda') !== false || stripos($text, 'technical certification') !== false) {
            $result['highest_education'] = 'Vocational / Technical Certification';
        } elseif (stripos($text, 'high school') !== false || stripos($text, 'secondary') !== false) {
            $result['highest_education'] = 'Secondary (High School)';
        }

        // Detect known universities
        $knownInstitutions = [
            'Western Mindanao State University',
            'University of the Philippines',
            'Ateneo de Manila University',
            'De La Salle University',
            'University of Santo Tomas',
            'Polytechnic University of the Philippines',
            'Zamboanga Peninsula Polytechnic State University',
            'Ateneo de Zamboanga University',
            'Universidad de Zamboanga',
        ];

        foreach ($knownInstitutions as $inst) {
            if (stripos($text, $inst) !== false) {
                $result['institution_name'] = $inst;
                break;
            }
        }

        // If not found, look for pattern "X University" or "X College"
        if (empty($result['institution_name'])) {
            if (preg_match('/([A-Z][a-zA-Z\s]+(?:University|College|Institute|Academy|School))/i', $text, $m)) {
                $result['institution_name'] = trim($m[1]);
            }
        }

        // Graduation Year (e.g., 2021-2025 or Class of 2024)
        if (preg_match('/(?:graduated|class of|batch|–|-|\sto\s)?\s*(20[12]\d)\b/i', $text, $m)) {
            $result['year_graduated'] = $m[1];
        }

        // Degree (e.g. BS Information Technology, BS Computer Science)
        if (preg_match('/(Bachelor of [^\n,]+|BS [^\n,]+|Associate in [^\n,]+)/i', $text, $m)) {
            $result['degree'] = trim($m[1]);
        }

        return $result;
    }

    protected function extractExperiences(array $lines, string $text): array
    {
        $experiences = [];

        // Check for common patterns: Job Title at Company (Year - Year)
        // Example: "Frontend Development Intern, Inclusive Web Studio (2025 - 2026)"
        // Or: "Software Engineer Intern at AKA (2024 - Present)"
        $expPattern = '/([A-Z][a-zA-Z\s]+(?:Intern|Developer|Engineer|Specialist|Associate|Assistant|Clerk|Coordinator|Manager|Analyst))\s+(?:at|–|-|,)\s+([A-Z0-9][a-zA-Z0-9\s\.\'&]+?)\s*[\(\[]?(\d{4})\s*(?:–|-|to)\s*(Present|\d{4})[\)\]]?/i';

        if (preg_match_all($expPattern, $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $isPresent = strtolower($m[4]) === 'present';
                $experiences[] = [
                    'job_title' => trim($m[1]),
                    'company_name' => trim($m[2]),
                    'location' => 'Philippines',
                    'start_year' => $m[3],
                    'end_year' => $isPresent ? '' : $m[4],
                    'is_current' => $isPresent,
                    'is_present' => $isPresent,
                    'description' => 'Contributed to projects and team deliverables with accessibility best practices.',
                ];
            }
        }

        // If regex didn't catch, parse under "Experience" header
        if (empty($experiences)) {
            $inExp = false;
            $currentTitle = '';
            $currentCompany = '';
            $currentDesc = [];

            foreach ($lines as $line) {
                $clean = strtolower(trim($line, " \t\n\r\0\x0B:"));
                if (in_array($clean, ['experience', 'work experience', 'employment history', 'internship experience'], true)) {
                    $inExp = true;
                    continue;
                }

                if ($inExp) {
                    if (preg_match('/^(education|skills|certifications|projects|awards|references)/i', $line)) {
                        break;
                    }

                    if (preg_match('/(\d{4})\s*(?:–|-|to)\s*(Present|\d{4})/i', $line, $ym)) {
                        $startYear = $ym[1];
                        $isPresent = strtolower($ym[2]) === 'present';
                        $endYear = $isPresent ? '' : $ym[2];

                        $experiences[] = [
                            'job_title' => $currentTitle ?: 'Professional Contributor',
                            'company_name' => $currentCompany ?: 'Inclusive Workplace',
                            'location' => 'Philippines',
                            'start_year' => $startYear,
                            'end_year' => $endYear,
                            'is_current' => $isPresent,
                            'is_present' => $isPresent,
                            'description' => implode(' ', $currentDesc) ?: 'Collaborated on projects and operational support.',
                        ];
                        $currentTitle = '';
                        $currentCompany = '';
                        $currentDesc = [];
                    } elseif (empty($currentTitle) && strlen($line) <= 60 && ! preg_match('/^\s*[-•*]/', $line)) {
                        $currentTitle = $line;
                    } elseif (empty($currentCompany) && strlen($line) <= 60 && ! preg_match('/^\s*[-•*]/', $line)) {
                        $currentCompany = $line;
                    } else {
                        $currentDesc[] = ltrim($line, "-•* \t");
                    }
                }
            }
        }

        return $experiences;
    }

    protected function extractLanguages(string $text): array
    {
        $languages = [];
        $candidates = ['English', 'Tagalog', 'Chavacano', 'Cebuano', 'FSL', 'Ilocano', 'Bicolano', 'Waray'];

        foreach ($candidates as $lang) {
            if (preg_match('/\b' . preg_quote($lang, '/') . '\b/i', $text)) {
                $languages[] = $lang;
            }
        }

        if (empty($languages)) {
            $languages = ['English', 'Tagalog'];
        }

        return array_values(array_unique($languages));
    }

    protected function extractAccommodations(string $text): array
    {
        $suggested = [];

        $mappings = [
            'Wheelchair Ramp Access' => ['wheelchair', 'ramp', 'step-free', 'elevator', 'mobility aid', 'paraplegia', 'cerebral palsy'],
            'Accessible Restroom' => ['accessible restroom', 'accessible toilet', 'grab bars', 'barrier-free restroom'],
            'Adjustable Workstation' => ['adjustable desk', 'ergonomic desk', 'height-adjustable', 'workstation'],
            'Screen Reader Compatibility' => ['screen reader', 'nvda', 'jaws', 'voiceover', 'talkback', 'blind', 'visual impairment'],
            'High-Contrast Displays' => ['high contrast', 'low vision', 'magnifier', 'visual clarity'],
            'Large Print Materials' => ['large print', 'large font', 'zoomed text'],
            'FSL Bilingual Staff' => ['fsl', 'filipino sign language', 'sign language', 'deaf', 'hard of hearing'],
            'Live Captions' => ['captions', 'subtitles', 'closed captions', 'captioned meetings'],
            'Visual Alerts' => ['visual alerts', 'flashing alerts', 'visual notifications'],
            'Quiet workspace' => ['quiet workspace', 'low sensory', 'noise-cancelling', 'adhd', 'autism'],
            'Flexible schedule' => ['flexible schedule', 'flexible hours', 'therapy visits', 'medical appointments'],
            'Remote work option' => ['remote work', 'work from home', 'hybrid schedule'],
        ];

        foreach ($mappings as $accName => $keywords) {
            foreach ($keywords as $kw) {
                if (stripos($text, $kw) !== false) {
                    $acc = Accommodation::where('name', $accName)->first();
                    if ($acc) {
                        $suggested[] = $acc->id;
                    }
                    break;
                }
            }
        }

        return array_values(array_unique($suggested));
    }
}
