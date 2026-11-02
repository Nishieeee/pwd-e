# TASKS.md

Deadline: core features working by **December 1, 2026**. All ten features targeted by **Nov 22**. See `ROADMAP.md`.

## Shared (before Nov 4)
- [ ] Confirm database engine and the "Proposed" decisions in `DECISIONS.md`
- [ ] Assign owners to tracks A–D
- [ ] Create the repo with `main` and `develop`; agree on the `accommodations` table and `MatchScorer` interface

## Track A: Foundation and Admin
- [ ] Scaffold Laravel 11 with Breeze (Blade) and Livewire (**by Nov 4**)
- [ ] spatie/laravel-permission with Candidate, Employer, Admin roles
- [ ] Migrations from `DATABASE.md`; seeders for accommodations, lexicon, admin account
- [ ] Verification (#9): private document upload, admin approve/reject, block unverified accounts
- [ ] Accessibility auditor (#10): axe-core JS log endpoint, `accessibility_logs`, admin log view
- [ ] Analytics dashboard (#8): hiring trends by sector, PWD employment rate, simple forecast
- [ ] Demo data seeders
- [ ] Privacy hardening: encrypted casts, policies, signed URLs, audit logging
- [ ] Deploy or demo setup

## Track B: Employer
- [ ] Workspace accessibility matrix (#6)
- [ ] Posting create/edit/publish
- [ ] Exclusionary language detector (#5): lexicon + regex, live flags and suggestions
- [ ] Applicant list and status changes
- [ ] Hire flow writing `workforce_records`
- [ ] RA 10524 tracker (#7): PWD share calculation and PDF report

## Track C: Candidate
- [ ] Profile form with accessibility needs (required vs. preferred) and per-field visibility
- [ ] FSL video record/upload (#4) attached to applications
- [ ] Voice-activated profile builder (#1): Web Speech API with form fallback
- [ ] Candidate dashboard: matches, applications, status

## Track D: Matching and UI
- [ ] Base layout with semantic landmarks
- [ ] Display toggles (#3): high contrast, text scale, dyslexia font, saved per user
- [ ] Matching service (#2): hard filter, weighted score, stored in `matches`
- [ ] Ranked postings page with "matches X of Y needs" explanation
- [ ] Apply to postings
- [ ] Full-loop feature tests and policy tests
- [ ] axe-core and NVDA passes (Nov 16–22 and Nov 23–29)
- [ ] Documentation updates

## Checkpoints
- [ ] Nov 4: scaffold pushed
- [ ] Nov 8: verification, matrix, profile form, layout merged
- [ ] Nov 15: detector, FSL, voice, matching, auditor merged; progress check against the fallback order
- [ ] Nov 22: all ten features working end to end
- [ ] Nov 29: hardened and demo-ready
- [ ] Dec 1: deadline

## Done
- [x] Project proposal submitted
- [x] Stack decision (Laravel + Blade + Livewire)
- [x] Planning docs written
