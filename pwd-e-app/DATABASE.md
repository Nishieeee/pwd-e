# DATABASE.md

Authoritative schema reference. Synced against PostgreSQL 18 on 2026-11-12.
_Confirm before writing new migrations._

---

## Application Tables

### `users` (17 cols)
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| name | varchar | composed full name for display |
| first_name | varchar | required at registration |
| middle_name | varchar | optional |
| last_name | varchar | required at registration |
| email | varchar | unique |
| email_verified_at | timestamp | Laravel default |
| password | varchar | bcrypt ≥ 12 |
| role | varchar | candidate / employer / admin |
| status | varchar | active / suspended / pending |
| verified_at | timestamp | set by admin on approval |
| display_settings | json | accessibility panel preferences (contrast, scale, font, motion) |
| remember_token | varchar | |
| created_at / updated_at | timestamp | |
| pwd_id_path | varchar | private disk path |
| pwd_id_number | varchar | encrypted cast |

### `candidate_profiles` (17 cols)
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| user_id | bigint FK → users | |
| headline | varchar | nullable |
| bio | text | nullable |
| location | varchar | nullable |
| skills | json | array of skill strings |
| fsl_video_path | varchar | private disk |
| fsl_caption_path | varchar | caption/transcript file |
| visibility_settings | json | per-field visibility map |
| created_at / updated_at | timestamp | |
| resume_pdf_path | varchar | private disk |
| disability_type | varchar | encrypted cast |
| disability_description | text | encrypted cast, optional self-description |
| phone | varchar | nullable |
| date_of_birth | date | nullable |
| gender | varchar | candidate-defined |

### `employer_profiles` (13 cols)
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| user_id | bigint FK → users | |
| company_name | varchar | |
| business_permit_number | varchar | |
| business_permit_path | varchar | private disk |
| verified_at | timestamp | null until admin approval |
| industry | varchar | nullable |
| location | varchar | nullable |
| workforce_total | integer | default 0 |
| workforce_updated_at | timestamp | nullable |
| matrix_completed_at | timestamp | nullable — set when accessibility matrix is filled |
| created_at / updated_at | timestamp | |

### `accommodations`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| name | varchar | |
| category | varchar | physical / sensory / schedule / tech |
| created_at / updated_at | timestamp | |

### `accessibility_needs`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| candidate_profile_id | bigint FK | |
| accommodation_id | bigint FK | |
| is_required | boolean | true = hard filter, false = soft preference |
| visible_to_employers | boolean | |
| created_at / updated_at | timestamp | |

### `employer_accommodations`
Pivot — what an employer **generally** provides across their workplace.
| Column | Type |
|---|---|
| id | bigint PK |
| employer_profile_id | bigint FK |
| accommodation_id | bigint FK |
| created_at / updated_at | timestamp |

### `job_accommodations`
Pivot — what a **specific job posting** provides (may differ from employer-wide matrix).
| Column | Type |
|---|---|
| id | bigint PK |
| job_posting_id | bigint FK |
| accommodation_id | bigint FK |
| created_at / updated_at | timestamp |

### `job_postings` (15 cols)
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| employer_profile_id | bigint FK | |
| title | varchar | |
| description | text | |
| required_skills | json | |
| location | varchar | nullable |
| work_arrangement | varchar | remote / onsite / hybrid |
| employment_type | varchar | full-time / part-time / contract / volunteer |
| salary_min / salary_max | numeric | nullable |
| closes_at | date | nullable |
| status | varchar | draft / pending_scan / published / closed / archived |
| scan_result | json | nullable — language detector output |
| scanned_at | timestamp | nullable |
| published_at | timestamp | nullable |
| deleted_at | timestamp | soft delete |
| created_at / updated_at | timestamp | |

### `matches`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| candidate_profile_id | bigint FK | |
| job_posting_id | bigint FK | unique pair |
| accessibility_score | numeric | 0–1 |
| skills_score | numeric | 0–1 |
| total_score | numeric | 0.6×a + 0.4×s |
| computed_at | timestamp | |
| created_at / updated_at | timestamp | |

### `applications` (13 cols)
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| candidate_profile_id | bigint FK | |
| job_posting_id | bigint FK | unique pair |
| status | varchar | submitted / under_review / interview / hired / rejected |
| cover_letter | text | encrypted cast |
| employer_notes | text | internal reviewer notes |
| fsl_video_path | varchar | application-level FSL (nullable) |
| fsl_caption_path | varchar | nullable |
| fit_score_at_apply | numeric | snapshot of score at time of apply |
| applied_at | timestamp | |
| deleted_at | timestamp | soft delete |
| created_at / updated_at | timestamp | |

### `workforce_records` (10 cols)
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| employer_profile_id | bigint FK | |
| candidate_profile_id | bigint FK | nullable |
| application_id | bigint FK | nullable — links to hiring application |
| hired_at | date | nullable |
| ended_at | date | nullable — when employment ended |
| pwd_verified | boolean | default false |
| annual_wage | numeric | nullable — used for RA 10524 deduction calculation |
| created_at / updated_at | timestamp | |

### `verification_requests` (11 cols)
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| user_id | bigint FK | |
| type | varchar | pwd_id / business_permit |
| id_number | varchar | nullable — PWD ID or permit number |
| document_path | varchar | private disk |
| status | varchar | pending / approved / rejected / needs_more |
| rejection_reason | varchar | nullable |
| reviewed_by | bigint FK → users | nullable |
| reviewed_at | timestamp | nullable |
| created_at / updated_at | timestamp | |

### `exclusionary_terms`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| phrase | varchar | |
| category | varchar | nullable |
| suggestion | varchar | nullable — inclusive replacement |
| is_active | boolean | soft-disable without deleting |
| created_at / updated_at | timestamp | |

### `accessibility_logs` (10 cols)
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| user_id | bigint FK | nullable |
| source | varchar | axe-core / pa11y / manual |
| page | varchar | |
| metric | varchar | |
| severity | varchar | critical / serious / moderate / minor |
| details | json | nullable |
| reviewed_by | bigint FK | nullable |
| reviewed_at | timestamp | nullable |
| created_at | timestamp | |

---

## Tables Added 2026-11-12 ✅

| Table | Purpose | Req IDs |
|---|---|---|
| `candidate_work_experiences` | Work history rows (job_title, company_name, dates, description) | PROF-01 |
| `candidate_education` | Education history rows (institution, degree, dates) | PROF-01 |
| `candidate_certifications` | Certifications (name, issuer, issued/expires, credential) | PROF-01 |
| `profile_drafts` | Auto-save wizard progress (user_id, step, data json, updated_at) | PROF-10 |
| `user_word_history` | Personal predictive text vocabulary (user_id, word, frequency, last_used_at) | ASSIST-03 |
| `tax_incentive_snapshots` | RA 10524 report snapshots (employer_profile_id, snapshot_date, pwd_count, deduction_basis json) | TAX-03 |
| `notifications` | In-app bell notifications (uuid id, type, notifiable morph, data, read_at) | NOTIF-02 |
| `notification_preferences` | Per-user, per-event email toggle (user_id, event_type, email_enabled) | NOTIF-03 |

---

## Laravel Framework Tables (do not modify)
`jobs`, `job_batches`, `failed_jobs`, `cache`, `cache_locks`, `sessions`, `password_reset_tokens`, `migrations`

## Spatie Permission Tables (do not modify directly)
`roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`

---

## Relationships

- `User` 1-1 `CandidateProfile` or `EmployerProfile` (by role)
- `User` 1-N `VerificationRequest`, `Notification`, `NotificationPreference`, `UserWordHistory`
- `CandidateProfile` 1-N `AccessibilityNeed`, `Application`, `Match`
- `AccessibilityNeed` N-1 `Accommodation`
- `EmployerProfile` N-N `Accommodation` via `employer_accommodations`
- `EmployerProfile` 1-N `JobPosting`, `WorkforceRecord`, `TaxIncentiveSnapshot`
- `JobPosting` N-N `Accommodation` via `job_accommodations`
- `JobPosting` 1-N `Match`, `Application`
- `Application` → `WorkforceRecord` (on hire)

---

## Data Rules

- **Encryption:** `users.pwd_id_number`, `candidate_profiles.disability_type`, `candidate_profiles.disability_description`, `applications.cover_letter` — Laravel encrypted casts (SEC-01).
- **Private disk:** all `*_path` columns — served only via signed, 1-hour expiring URLs (SEC-02).
- **Visibility:** `accessibility_needs.visible_to_employers` and `candidate_profiles.visibility_settings` enforced in Policies and query scopes, never just in UI (PROF-06).
- **Unique constraints:** (`candidate_profile_id`, `job_posting_id`) on `matches` and `applications`; (`user_id`, `word`) on `user_word_history`.
- **Unverified gate:** `users.verified_at IS NULL` → cannot apply, post, or appear in listings (AUTH-04).
- **Soft deletes:** `job_postings`, `applications`; keep `workforce_records` permanently for RA 10524 reporting.
- **Indexes:** all FKs; `job_postings.status`; `applications.status`; `notifications.(user_id, read_at)`; `user_word_history.(user_id, word)`.
- Every read of a `*_path` or disability field writes an `activity_log` entry (SEC-03).
