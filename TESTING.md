# TESTING.md

## Commands
```bash
php artisan test                 # full suite
php artisan test --filter=Match  # a subset
./vendor/bin/pint --test         # style check
npx pa11y-ci                     # automated accessibility scan (once configured)
```

## What must be tested
| Area | Checks |
|---|---|
| Roles and policies | Each role sees only its pages; candidates' hidden fields never reach employers |
| Verification gating | Unverified accounts cannot post, apply, or appear in listings |
| Matching | Hard filter excludes postings missing a required accommodation; score math; threshold behavior |
| Language detector | Known exclusionary phrases flagged; clean text not flagged |
| Compliance report | PWD share calculation and export contents |
| File access | Private files require a signed URL or authorization |

## Accessibility testing
- **Automated:** axe-core and Pa11y on every main page; no new violations allowed.
- **Manual (per feature):**
  - Keyboard only: reach and operate everything, with visible focus
  - NVDA with Firefox or Chrome: page title announced, headings and landmarks make sense, live updates announced
  - Zoom to 200% and test text scaling
  - High-contrast and dyslexia-font toggles
  - Voice input fallback: the form works without a microphone

## Before marking a task done
Tests pass, Pint is clean, axe shows no new issues, and the manual checklist above was run for any UI change.
