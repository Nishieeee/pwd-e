# ROADMAP.md

**Hard deadline: core features working by December 1, 2026.** Today is November 2 (about four weeks).

## Strategy: parallel tracks, all modules early
Instead of finishing one module at a time, the four team members each own a track and work in parallel. The target is to have **all ten features working in thin form by Nov 22**, leaving the last week for hardening, testing, and bug fixes. "Thin" means it works end to end with the simplest approach; polish comes after Dec 1.

## Tracks
Assign a team member to each track.

| Track | Owner | Modules and features |
|---|---|---|
| **A. Foundation and Admin** | _TBD_ | Scaffold, auth, roles, verification (#9), accessibility auditor (#10), analytics (#8) |
| **B. Employer** | _TBD_ | Accessibility matrix (#6), postings and language detector (#5), hire flow and RA 10524 tracker (#7) |
| **C. Candidate** | _TBD_ | Profile form, needs and visibility, voice builder (#1), FSL video (#4), candidate dashboard |
| **D. Matching and UI** | _TBD_ | Base layout and display toggles (#3), matching (#2), applications, integration and accessibility testing |

## Weekly plan
| Week | Dates | A. Foundation / Admin | B. Employer | C. Candidate | D. Matching / UI |
|---|---|---|---|---|---|
| 1 | Nov 2–8 | Scaffold, auth, roles by **Nov 4** and push; then verification queue (#9) | Matrix (#6): migrations, models, onboarding form | Profile form, accessibility needs, visibility toggles | Base layout, display toggles (#3); matching service skeleton with unit tests on fake data |
| 2 | Nov 9–15 | Auditor (#10): axe-core log endpoint and admin log view | Posting editor and language detector (#5); applicant list | FSL record/upload (#4); voice input (#1) | Matching (#2) wired to real matrix and needs; ranked results page; applications |
| 3 | Nov 16–22 | Analytics dashboard (#8); demo data seeders | Hire flow; RA 10524 tracker and PDF (#7) | Candidate dashboard and application status; voice/FSL fixes | Full-loop integration; feature tests; first axe-core and NVDA pass |
| 4 | Nov 23–29 | Privacy hardening (encrypted casts, policies, private files); deploy or demo setup | Bug fixes; report accuracy checks | Bug fixes; accessibility fixes from the audit | End-to-end test of the loop; second accessibility pass; documentation |
| Buffer | Nov 30–Dec 1 | Bug fixes only; freeze; rehearse the demo | | | |

## Milestones
| Date | Milestone |
|---|---|
| Nov 4 | Scaffold pushed; roles and auth working; everyone branches from `develop` |
| Nov 8 | Verification, matrix, profile form, layout all merged |
| Nov 15 | Language detector, FSL, voice, matching, auditor merged |
| Nov 22 | **All ten features working end to end (thin)** |
| Nov 29 | Hardened, tested, demo-ready |
| Dec 1 | Deadline |

## Rules to protect the deadline
- Track A ships the scaffold within the first two days; until then, others work on migrations, mockups, and service classes that don't need the full app.
- Merge to `develop` at least every few days. Do not leave integration for the last week.
- Agree on shared interfaces early: the `accommodations` table, the `MatchScorer` contract, and role names.
- Cut scope, not accessibility or privacy.

## Fallback if behind
Check progress on **Nov 15**. If tracks are behind, cut in this order: #8 analytics, #10 auditor, polish on #7, then #1 voice. Never cut the core loop (verification, matrix, postings, profile, matching, applications).

## Later additions
- Whisper-based speech-to-text for better Filipino/Taglish accuracy
- Python NLP service for the language detector
- Notification emails and SMS
