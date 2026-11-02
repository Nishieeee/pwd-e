# DATABASE.md

Draft schema. Confirm before writing migrations.

## Tables
| Table | Key columns |
|---|---|
| `users` | id, name, email, password, role, verified_at |
| `candidate_profiles` | user_id, headline, skills (json), bio, fsl_video_path, visibility_settings (json) |
| `accommodations` | id, name, category (physical, sensory, schedule, tech) |
| `accessibility_needs` | id, candidate_profile_id, accommodation_id, is_required, visible_to_employers |
| `employer_profiles` | user_id, company_name, business_permit_path, verified_at, workforce_total |
| `employer_accommodations` | employer_profile_id, accommodation_id |
| `job_postings` | id, employer_profile_id, title, description, required_skills (json), status, scan_result (json) |
| `matches` | id, candidate_profile_id, job_posting_id, accessibility_score, skills_score, total_score |
| `applications` | id, candidate_profile_id, job_posting_id, status, applied_at |
| `workforce_records` | employer_profile_id, user_id, hired_at, pwd_verified |
| `verification_requests` | id, user_id, document_path, status, reviewed_by, reviewed_at |
| `exclusionary_terms` | id, phrase, category, suggestion |
| `accessibility_logs` | id, page, metric, details (json), created_at |
| `activity_log` | provided by spatie/laravel-activitylog |

## Relationships
- `User` 1-1 `CandidateProfile` or `EmployerProfile` (by role)
- `CandidateProfile` 1-N `AccessibilityNeed`, N-1 `Accommodation`
- `EmployerProfile` N-N `Accommodation` via `employer_accommodations`
- `EmployerProfile` 1-N `JobPosting`
- `CandidateProfile` N-N `JobPosting` via `matches` and `applications`

## Data rules
- Disability-related fields (`accessibility_needs`, any disability description) use encrypted casts.
- `visible_to_employers` is enforced in query scopes and Policies.
- Unique constraints: (`candidate_profile_id`, `job_posting_id`) on `matches` and `applications`.
- Unverified users (`verified_at` null) cannot apply, post, or appear in listings.
- Document and video paths point to the private disk; never store public URLs.
- Soft-delete postings and applications; keep `workforce_records` for reporting.
- Index foreign keys and `job_postings.status`.
