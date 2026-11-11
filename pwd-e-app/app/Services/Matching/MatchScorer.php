<?php

namespace App\Services\Matching;

interface MatchScorer
{
    /**
     * Compute a match score between a candidate profile and a job posting.
     *
     * @return array{accessibility_score: float, skills_score: float, total_score: float}
     */
    public function score(int $candidateProfileId, int $jobPostingId): array;
}
