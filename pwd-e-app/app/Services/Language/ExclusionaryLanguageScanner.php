<?php

namespace App\Services\Language;

use App\Models\ExclusionaryTerm;

class ExclusionaryLanguageScanner
{
    /**
     * Scan text for active exclusionary terms.
     *
     * @return array{
     *     has_flags: bool,
     *     flags: array<int, array{
     *         id: int,
     *         phrase: string,
     *         suggestion: string,
     *         category: string|null,
     *         occurrences: int
     *     }>
     * }
     */
    public function scan(string $text): array
    {
        $activeTerms = ExclusionaryTerm::active()->get();
        $flags = [];

        foreach ($activeTerms as $term) {
            $phrase = trim($term->phrase);
            if ($phrase === '') {
                continue;
            }

            // Word-boundary case-insensitive pattern
            $pattern = '/\b' . preg_quote($phrase, '/') . '\b/i';
            $matchCount = preg_match_all($pattern, $text);

            if ($matchCount > 0) {
                $flags[] = [
                    'id' => $term->id,
                    'phrase' => $phrase,
                    'suggestion' => $term->suggestion ?? '',
                    'category' => $term->category,
                    'occurrences' => $matchCount,
                ];
            }
        }

        return [
            'has_flags' => count($flags) > 0,
            'flags' => $flags,
        ];
    }

    /**
     * Replace a phrase with its inclusive suggestion in the given text.
     */
    public function applySuggestion(string $text, string $phrase, string $suggestion): string
    {
        $pattern = '/\b' . preg_quote(trim($phrase), '/') . '\b/i';

        return (string) preg_replace($pattern, $suggestion, $text);
    }
}
