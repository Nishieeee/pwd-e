# PWD-E: A Job Portal for People with Disabilities

**Course:** Software Engineering 1 (BSCS, WMSU College of Computing Studies)
**Team:** Bongabong, Goberlyn E. · Jandugan, Jovel Joy L. · Pagarogan, Jhon Clein T. · Sintoy, Joebert S.
**Instructors:** Engr. Marjorie Rojas · Mr. Vin Czar R. Jailani
**Status:** Implementation starting; core features due December 1, 2026

---

## 1. Overview

PWD-E is an accessibility-first, web-based job portal that connects people with disabilities (PWDs) to employers whose workplaces can accommodate them. A job match must consider **accessibility, not skills alone**.

Candidates build profiles by voice or form and may attach a Filipino Sign Language (FSL) video. Employers declare their workplace accommodations and have their postings scanned for exclusionary wording. The system produces an **accessibility-fit score** per candidate–posting pair. Employers and PESO/LGU administrators get tools for RA No. 10524 compliance reporting, account verification, analytics, and platform accessibility monitoring.

## 2. Users and Roles

| Role | Description | Main functions |
|---|---|---|
| Candidate (PWD) | Job seeker with a disability | Builds profile by voice/form, states accessibility requirements, attaches FSL video, views fit scores, applies, adjusts display settings |
| Employer | Company or office that hires | Completes accessibility matrix, drafts scanned postings, reviews applicants, views PWD workforce share, generates RA 10524 reports |
| System Administrator (PESO/LGU) | Local employment office staff | Verifies PWD IDs and business permits, views analytics, reviews accessibility audit logs |

## 3. Features

| # | Feature | Module |
|---|---|---|
| 1 | Voice-activated profile builder | Candidate |
| 2 | Accessibility-fit matching algorithm | Candidate |
| 3 | WCAG 2.1 compliant interface | Candidate |
| 4 | FSL video resumes | Candidate |
| 5 | Exclusionary language detector | Employer |
| 6 | Workspace accessibility matrix | Employer |
| 7 | RA No. 10524 tax incentive tracker | Employer |
| 8 | Predictive employment analytics | Administrator |
| 9 | Account verification engine | Administrator |
| 10 | Platform accessibility auditor | Administrator |

## 4. Tech Stack

| Layer | Choice |
|---|---|
| Backend | Laravel 11 (PHP 8.2+) |
| Auth and roles | Laravel Breeze, spatie/laravel-permission |
| Database | MySQL 8 (or PostgreSQL) |
| Frontend | Blade, Livewire, Alpine.js, Tailwind CSS |
| Speech-to-text | Web Speech API (prototype); OpenAI Whisper (upgrade path) |
| Matching | Plain PHP/SQL weighted scoring (service class) |
| Language detector | Rule-based lexicon and regex; optional LLM call for rewrite suggestions |
| FSL video | Browser MediaRecorder, upload to private storage, FFmpeg compression |
| Reports | barryvdh/laravel-dompdf (PDF), maatwebsite/excel (XLSX) |
| Charts | Chart.js or ApexCharts |
| Accessibility auditing | axe-core, Lighthouse, Pa11y; JS error-logging endpoint |
| Audit logging | spatie/laravel-activitylog |
| Testing | Pest or PHPUnit, Laravel Dusk (browser tests) |
| Deployment | Docker on a VPS (or Railway/Render for demos) |

## 5. Architecture

A single Laravel monolith, organized by domain.

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Candidate/
│   │   ├── Employer/
│   │   └── Admin/
│   └── Middleware/          # role + verified-account checks
├── Livewire/                # profile builder, posting editor, dashboards
├── Models/
├── Policies/                # who can view which candidate field
├── Services/
│   ├── Matching/            # AccessibilityFitService, SkillsMatchService
│   ├── LanguageScan/        # ExclusionaryLanguageDetector
│   ├── Compliance/          # Ra10524ReportService
│   └── Analytics/
└── Jobs/                    # video compression, report generation
resources/
├── views/                   # semantic Blade templates
└── css/                     # theme tokens: contrast, scale, dyslexia font
database/
├── migrations/
└── seeders/                 # accommodations, lexicon, demo data
```

## 6. Data Model (draft)

| Table | Key columns |
|---|---|
| `users` | id, name, email, password, role, verified_at |
| `candidate_profiles` | user_id, headline, skills (json), bio, fsl_video_path, visibility_settings (json) |
| `accessibility_needs` | id, candidate_profile_id, accommodation_id, is_required (hard/soft), visible_to_employers |
| `accommodations` | id, name, category (physical, sensory, schedule, tech) |
| `employer_profiles` | user_id, company_name, business_permit_path, verified_at, workforce_total |
| `employer_accommodations` | employer_profile_id, accommodation_id |
| `job_postings` | id, employer_profile_id, title, description, required_skills (json), status, scan_result (json) |
| `matches` | id, candidate_profile_id, job_posting_id, accessibility_score, skills_score, total_score |
| `applications` | id, candidate_profile_id, job_posting_id, status, applied_at |
| `workforce_records` | employer_profile_id, user_id, hired_at, pwd_verified |
| `verification_requests` | id, user_id, document_path, status, reviewed_by, reviewed_at |
| `exclusionary_terms` | id, phrase, category, suggestion |
| `accessibility_logs` | id, page, metric, details (json), created_at |
| `activity_log` | from spatie/laravel-activitylog |

## 7. Matching Logic

1. **Hard filter:** exclude any posting that lacks an accommodation the candidate marked as *required*.
2. **Accessibility fit:** weighted share of the candidate's remaining (soft) needs that the employer provides.
3. **Skills match:** overlap between candidate skills and posting requirements.
4. **Total score:**

```
total = 0.6 × accessibility_fit + 0.4 × skills_match
```

Postings below a configurable threshold are hidden and replaced with other suggestions. The system shows an explanation such as "matches 5 of 6 needs".

## 8. Privacy and Security

Disability information is sensitive personal information under the Data Privacy Act of 2012 (RA 10173).

- Collect only the accessibility details needed for matching.
- Encrypt disability-related fields (Laravel encrypted casts).
- Candidates choose which details each employer can see (per-field visibility).
- Store PWD IDs, permits, and FSL videos on a private disk with signed, expiring URLs.
- Log access to sensitive records.
- Restrict access with role middleware and policies.
- Accounts stay inactive until verified by an administrator.

## 9. Accessibility Requirements (WCAG 2.1 AA)

- Semantic HTML, labelled form controls, ARIA only where needed
- Full keyboard navigation with visible focus states
- High-contrast, text-scaling, and dyslexia-friendly font toggles (CSS variables, saved per user)
- Captions and text alternatives for any media
- Automated checks (axe-core, Pa11y) in CI plus manual screen-reader testing (NVDA)

## 10. Success Metrics (from proposal)

- Time to complete a profile by voice vs. by form
- Share of flagged phrases in postings that employers revise
- Share of applications coming from postings with an acceptable fit score
- Accessibility errors logged by the auditor over time

## 11. Local Setup

```bash
git clone <repo-url> pwd-e && cd pwd-e
composer install
cp .env.example .env
php artisan key:generate

# configure DB_* in .env, then:
php artisan migrate --seed
php artisan storage:link

npm install && npm run dev
php artisan serve
```

Run the tests with `php artisan test`.

## 12. Team Conventions

- **Branching:** `main` (stable), `develop`, `feature/<name>`
- **Commits:** short imperative messages (e.g. `Add accessibility matrix form`)
- **Pull requests:** at least one teammate review before merging to `develop`
- **Definition of done:** feature works, tests pass, axe-core reports no new violations

## 13. Roadmap

**Core features are due December 1, 2026.** Four parallel tracks target all ten features (thin) by Nov 22, then harden and test through Nov 29. See `ROADMAP.md`.

| Track | Features |
|---|---|
| A. Foundation and Admin | Scaffold, auth, roles, verification (#9), accessibility auditor (#10), analytics (#8) |
| B. Employer | Accessibility matrix (#6), postings and language detector (#5), RA 10524 tracker (#7) |
| C. Candidate | Profile form, voice builder (#1), FSL video (#4) |
| D. Matching and UI | WCAG interface (#3), matching (#2), applications, integration testing |

## 14. References

- Implementing Rules and Regulations of Republic Act No. 10524 (2016). Supreme Court E-Library.
- Philippine Statistics Authority (2022). *Special release on functional difficulty: 2020 Census of Population and Housing.*
- Republic Act No. 10173 (2012). *Data Privacy Act of 2012.*
- Republic Act No. 11106 (2018). *The Filipino Sign Language Act.*
- World Health Organization (2023). *Disability* [Fact sheet].

## 15. Related Docs

`AGENTS.md` (start here) · `ARCHITECTURE.md` · `CURRENT-STATE.md` · `DECISIONS.md` · `CONVENTIONS.md` · `DATABASE.md` · `TESTING.md` · `TASKS.md` · `ROADMAP.md` · `HANDOFF.md`
