# DATABASE.md

Draft schema. Confirm before writing new migrations.
_Last updated: 2026-11-11 — added `pwd_id_path` to `users`, `user_word_history` table for predictive text, `tax_incentive_snapshots` for RA 10524 reporting._

---

## Tables

| Table | Key columns |
|---|---|
| `users` | id, name, email, password, role, **pwd_id_path** (private disk), **pwd_id_number**, verified_at, suspended_at |
| `candidate_profiles` | user_id, headline, skills (json), bio, fsl_video_path, resume_pdf_path, visibility_settings (json) |
| `accommodations` | id, name, category (physical, sensory, schedule, tech) |
| `accessibility_needs` | id, candidate_profile_id, accommodation_id, is_required (bool: hard/soft), visible_to_employers |
| `employer_profiles` | user_id, company_name, business_permit_path, business_permit_number, verified_at, workforce_total |
| `employer_accommodations` | employer_profile_id, accommodation_id |
| `job_postings` | id, employer_profile_id, title, description (text), required_skills (json), employment_type, location, salary_min, salary_max, status (draft/pending_scan/published/closed/archived), scan_result (json), deleted_at |
| `matches` | id, candidate_profile_id, job_posting_id, accessibility_score, skills_score, total_score, computed_at |
| `applications` | id, candidate_profile_id, job_posting_id, cover_letter (text, encrypted), status (submitted/under_review/interview/hired/rejected), applied_at, deleted_at |
| `application_documents` | id, application_id, type (pwd_id/resume/fsl_video), file_path, uploaded_at |
| `workforce_records` | id, employer_profile_id, user_id, hired_at, pwd_verified, left_at |
| `verification_requests` | id, user_id, document_type (pwd_id/business_permit), document_path, status (pending/approved/rejected/needs_more), reviewed_by, reviewed_at, notes |
| `exclusionary_terms` | id, phrase, category, suggestion, created_by, updated_at |
| `accessibility_logs` | id, page, metric, details (json), created_at |
| `notification_preferences` | id, user_id, event_type, email_enabled |
| `notifications` | id, user_id, type, data (json), read_at, created_at |
| **`user_word_history`** | id, user_id, word, frequency, last_used_at — _powers personal predictive text vocabulary (ASSIST-03)_ |
| **`tax_incentive_snapshots`** | id, employer_profile_id, snapshot_date, pwd_count, deduction_basis (json), generated_by — _for RA 10524 PDF/XLSX (TAX-03)_ |
| `activity_log` | provided by spatie/laravel-activitylog |
| `profile_drafts` | id, user_id, step, data (json), updated_at — _auto-save every 60 s (PROF-10)_ |

---

## Relationships

- `User` 1-1 `CandidateProfile` or `EmployerProfile` (by role)
- `User` 1-N `VerificationRequest`, 1-N `Notification`, 1-N `NotificationPreference`, 1-N `UserWordHistory`
- `CandidateProfile` 1-N `AccessibilityNeed`, 1-N `Application`, 1-N `Match`
- `AccessibilityNeed` N-1 `Accommodation`
- `EmployerProfile` N-N `Accommodation` via `employer_accommodations`
- `EmployerProfile` 1-N `JobPosting`, 1-N `WorkforceRecord`, 1-N `TaxIncentiveSnapshot`
- `JobPosting` 1-N `Match`, 1-N `Application`
- `Application` 1-N `ApplicationDocument`

---

## Data Rules

- **Encryption:** `accessibility_needs` (all columns), `users.pwd_id_number`, `applications.cover_letter`, `users.pwd_id_path` — use Laravel encrypted casts (SEC-01).
- **Private disk:** `pwd_id_path`, `business_permit_path`, `fsl_video_path`, `resume_pdf_path`, all `application_documents.file_path` — served only via signed, 1-hour expiring URLs (SEC-02).
- **Visibility:** `visible_to_employers` enforced in query scopes and Policies; `profile_visibility_settings` JSON on candidate controls per-field access (PROF-06).
- **Unique constraints:** (`candidate_profile_id`, `job_posting_id`) on `matches` and `applications`.
- **Unverified gate:** `verified_at IS NULL` → cannot apply, post jobs, or appear in listings (AUTH-04).
- **Soft deletes:** `job_postings`, `applications`; keep `workforce_records` permanently for RA 10524 reporting.
- **Indexes:** all foreign keys; `job_postings.status`; `applications.status`; `user_word_history.(user_id, word)` unique; `notifications.(user_id, read_at)`.
- Audit log: every read of a document path or disability field writes an `activity_log` entry (SEC-03, ADMIN-05).
