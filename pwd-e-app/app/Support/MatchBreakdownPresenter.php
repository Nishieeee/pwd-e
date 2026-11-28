<?php

namespace App\Support;

use App\Models\CandidateProfile;
use App\Models\JobPosting;

class MatchBreakdownPresenter
{
    /**
     * Build the SE1 UI/UX progressive-disclosure match breakdown payload
     * focused strictly on Skill & Achievement factors + Accessibility Fit.
     *
     * @param  array<int, int|string>|null  $activeAccommodationIds
     * @return array{
     *     overall_pct: int,
     *     accessibility_pct: int,
     *     skills_achievement_pct: int,
     *     pillars: array<int, array{
     *         key: string,
     *         label: string,
     *         badge: string,
     *         pct: int,
     *         summary: string,
     *         matched_items: array<int, string>,
     *         extra_items: array<int, string>
     *     }>
     * }
     */
    public static function build(?CandidateProfile $profile, ?JobPosting $job, ?float $overallPct = null, ?array $activeAccommodationIds = null): array
    {
        $resolvedOverall = $overallPct !== null ? (int) round($overallPct) : 92;

        $jobSkills = is_array($job?->required_skills) ? array_values(array_filter(array_map('trim', $job->required_skills))) : [];
        $candidateSkills = is_array($profile?->skills) ? array_values(array_filter(array_map('trim', $profile->skills))) : [];

        $matchedSkills = [];
        $missingSkills = [];
        foreach ($jobSkills as $reqSkill) {
            $found = false;
            foreach ($candidateSkills as $cSkill) {
                if (strcasecmp($reqSkill, $cSkill) === 0 || stripos($cSkill, $reqSkill) !== false || stripos($reqSkill, $cSkill) !== false) {
                    $found = true;
                    break;
                }
            }
            if ($found) {
                $matchedSkills[] = $reqSkill;
            } else {
                $missingSkills[] = $reqSkill;
            }
        }

        $skillsPct = count($jobSkills) > 0
            ? (int) round((count($matchedSkills) / count($jobSkills)) * 100)
            : (! empty($candidateSkills) ? 90 : 75);

        // Accessibility / Workplace Accommodations Fit
        $jobAccs = $job?->relationLoaded('accommodations')
            ? $job->accommodations
            : ($job ? $job->accommodations()->get() : collect());
        $jobAccMap = $jobAccs->pluck('name', 'id')->all();

        $candidateNeedIds = $activeAccommodationIds !== null
            ? array_map('intval', $activeAccommodationIds)
            : ($profile
                ? ($profile->relationLoaded('accessibilityNeeds')
                    ? $profile->accessibilityNeeds->pluck('accommodation_id')->map(fn ($id) => (int) $id)->all()
                    : $profile->accessibilityNeeds()->pluck('accommodation_id')->map(fn ($id) => (int) $id)->all())
                : []);

        $supportedAccNames = [];
        $unsupportedCount = 0;
        foreach ($candidateNeedIds as $accId) {
            if (isset($jobAccMap[$accId])) {
                $supportedAccNames[] = $jobAccMap[$accId];
            } else {
                $unsupportedCount++;
            }
        }

        if (count($candidateNeedIds) > 0) {
            $accessibilityPct = (int) round((count($supportedAccNames) / count($candidateNeedIds)) * 100);
            $accSummary = count($supportedAccNames).' of '.count($candidateNeedIds).' required workplace accommodations supported';
        } else {
            $accessibilityPct = 100;
            $supportedAccNames = array_slice(array_values($jobAccMap), 0, 4);
            $accSummary = count($jobAccMap).' barrier-free workplace accommodations provided';
        }

        // Work Experience
        $experiences = $profile
            ? ($profile->relationLoaded('workExperiences') ? $profile->workExperiences : $profile->workExperiences()->get())
            : collect();
        $expTitles = $experiences->map(fn ($e) => trim($e->job_title.($e->company_name ? ' · '.$e->company_name : '')))->filter()->values()->all();
        $expPct = $experiences->isNotEmpty() ? min(100, 80 + ($experiences->count() * 10)) : 70;

        // Trainings & Seminars
        $trainings = $profile
            ? ($profile->relationLoaded('trainings') ? $profile->trainings : $profile->trainings()->get())
            : collect();
        $trainingTitles = $trainings->map(fn ($t) => trim($t->title.($t->organizer ? ' ('.$t->organizer.')' : '')))->filter()->values()->all();

        // Certifications, TESDA & Education
        $certs = $profile
            ? ($profile->relationLoaded('certifications') ? $profile->certifications : $profile->certifications()->get())
            : collect();
        $proCerts = $certs->filter(fn ($c) => ($c->category ?? 'certification') !== 'education_diploma');
        $certTitles = $proCerts->map(fn ($c) => trim($c->name.($c->issuing_organization ? ' · '.$c->issuing_organization : '')))->filter()->values()->all();
        if (! empty($profile?->highest_education)) {
            $certTitles[] = $profile->highest_education;
        }

        $achievementCount = $trainings->count() + $proCerts->count();
        $achievementPct = $achievementCount > 0 ? min(100, 82 + ($achievementCount * 6)) : (! empty($profile?->highest_education) ? 80 : 70);

        $skillsAchievementPct = (int) round(($skillsPct * 0.5) + ($expPct * 0.25) + ($achievementPct * 0.25));

        return [
            'overall_pct' => $resolvedOverall,
            'accessibility_pct' => $accessibilityPct,
            'skills_achievement_pct' => $skillsAchievementPct,
            'pillars' => [
                [
                    'key' => 'accommodations',
                    'label' => 'Accessibility & Accommodations Fit',
                    'badge' => count($candidateNeedIds) > 0 ? count($supportedAccNames).'/'.count($candidateNeedIds).' Supported' : 'Verified Matrix',
                    'pct' => $accessibilityPct,
                    'summary' => $accSummary,
                    'matched_items' => array_slice($supportedAccNames, 0, 5),
                    'extra_items' => $unsupportedCount > 0 ? [$unsupportedCount.' accommodation need(s) to discuss during interview'] : [],
                ],
                [
                    'key' => 'skills',
                    'label' => 'Skills & Core Competencies (Résumé)',
                    'badge' => count($jobSkills) > 0 ? count($matchedSkills).'/'.count($jobSkills).' Matched' : count($candidateSkills).' Skills',
                    'pct' => $skillsPct,
                    'summary' => count($jobSkills) > 0
                        ? count($matchedSkills).' of '.count($jobSkills).' job skill requirements found on candidate profile/résumé'
                        : 'Evaluated from candidate résumé skills',
                    'matched_items' => ! empty($matchedSkills) ? $matchedSkills : array_slice($candidateSkills, 0, 5),
                    'extra_items' => array_slice($missingSkills, 0, 3),
                ],
                [
                    'key' => 'experience_trainings',
                    'label' => 'Experience, Trainings & Seminars',
                    'badge' => $experiences->count().' Role(s) · '.$trainings->count().' Seminar(s)',
                    'pct' => (int) round(($expPct + ($trainings->isNotEmpty() ? 95 : 75)) / 2),
                    'summary' => ($experiences->count() > 0 || $trainings->count() > 0)
                        ? 'Includes '.$experiences->count().' work role(s) and '.$trainings->count().' training/seminar record(s)'
                        : 'Entry-level profile — add trainings or internships to strengthen match visibility',
                    'matched_items' => array_slice(array_merge($expTitles, $trainingTitles), 0, 4),
                    'extra_items' => [],
                ],
                [
                    'key' => 'certifications_education',
                    'label' => 'Certifications, TESDA & Education',
                    'badge' => $proCerts->count() > 0 ? $proCerts->count().' Credential(s)' : ($profile?->highest_education ? 'Education Verified' : 'Optional'),
                    'pct' => $achievementPct,
                    'summary' => $proCerts->count() > 0
                        ? $proCerts->count().' professional/TESDA certification(s) alongside educational attainment'
                        : ($profile?->highest_education ?: 'Educational attainment recorded'),
                    'matched_items' => array_slice($certTitles, 0, 4),
                    'extra_items' => [],
                ],
            ],
        ];
    }
}
