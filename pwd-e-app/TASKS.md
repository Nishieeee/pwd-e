# TASKS.md

Deadline: core features working by **December 1, 2026**. All features targeted by **Nov 22**. See `ROADMAP.md`.
_Last synced with `docs/REQUIREMENTS.md` v1.1 — 2026-11-11_

---

## Done ✅
- [x] Project proposal submitted
- [x] Stack decision (Laravel + Blade + Livewire)
- [x] Planning docs written
- [x] PostgreSQL 18 connected; core schema applied; seeders run (accommodations, lexicon, admin + roles)
- [x] Home, Jobs, Employers, About pages (static Blade, mobile-first)
- [x] Accessibility panel componentized (reusable)
- [x] Auth: Fortify scaffold + redesigned login page (1:1, mobile-first, live email validation)
- [x] Registration redesign: role picker (Candidate / Employer), conditional fields, PWD ID + business permit, disability type, DOB, consent; transactionally creates User + profile + VerificationRequest
- [x] Name parts persisted: `first_name`, `middle_name` (optional), `last_name` on `users`
- [x] Unverified account gate middleware (pending users redirected to verification notice)
- [x] Profile-completion schema: work experience, education, certifications, profile drafts, notifications, notification preferences, word history, tax snapshots + encrypted casts
- [x] `docs/ERD.md` created; `docs/REQUIREMENTS.md` v1.1 written (SRS from stakeholder interview)
- [x] Candidate portal shipped: profile builder (5-step wizard, PSGC zip auto-fill, resume PDF gen, FSL/PWD ID cards), dashboard, job matches, applications tracker (merged PR #9)

---

## Track A — Foundation & Admin

### Auth & Registration (AUTH-01–10)
- [x] Registration page redesign: role picker (Candidate / Employer) + auto-create profile rows (AUTH-01)
- [x] Candidate registration: collect disability type (dropdown/multi-select) + PWD ID number field (AUTH-02, AUTH-09)
- [x] Employer registration: collect company name, email, business permit number (AUTH-03)
- [x] Block unverified accounts from sensitive actions; redirect with friendly message (AUTH-04)
- [x] Email notification on account approval / rejection (AUTH-05) — covered by `VerificationStatusUpdated` notification fired in verification queue
- [ ] Password reset via time-limited token link (AUTH-06)
- [ ] Session expiry after 2 h inactivity with redirect + message (AUTH-07)
- [ ] "Remember me" 30-day persistent session (AUTH-10)

### Verification Queue (ADMIN-01–03) `#9`
- [x] Private document stream endpoint for PWD ID / business permit (AUTH-09, SEC-02)
- [x] Admin queue view: list requests with filter tabs + inline document preview (ADMIN-01, ADMIN-02)
- [x] Approve / reject / request-more-docs actions; notification fired on each (ADMIN-03, NOTIF-01)

### Admin Dashboard (ADMIN-04–08) `#8`
- [x] Platform analytics panel: users by role, apps/week, top employers (ADMIN-04) — a11y error rate pending (no data source yet)
- [x] Audit log view: filterable by user, action, date (ADMIN-05) — spatie/laravel-activitylog — `/admin/audit-log` (D-16)
- [x] Suspend / reinstate user (ADMIN-06) — `/admin/users`
- [ ] Scheduled axe-core / Pa11y report runner + admin panel view (ADMIN-07) `#10` — **deferred, panel placeholder on dashboard**
- [x] Exclusionary-language lexicon management UI: add / edit / remove terms (ADMIN-08) — `/admin/lexicon`

### Privacy & Security Hardening (SEC-01–09)
- [ ] Encrypted casts on disability fields (`accessibility_needs`, disability type) (SEC-01)
- [ ] Private disk + signed, expiring URLs (1 h) for documents and FSL video (SEC-02, SEC-03)
- [ ] Policies and role middleware on all routes (SEC-04)
- [ ] Server-side file upload validation: MIME, extension, size limits (SEC-05)
- [ ] Rate limiting on login (5/min), registration, password reset (SEC-07)
- [ ] Suppress stack traces in production (SEC-08)

### Notifications (NOTIF-01–03)
- [x] Email notification system (queued jobs) for account approved/rejected, application received, application status changed, verification document required (NOTIF-01)
- [x] In-app notification bell with unread count + mark-all-read (NOTIF-02)
- [ ] Notification preference settings per user (NOTIF-03) — deferred

### Infrastructure
- [x] Demo data seeders (candidates, employers, postings, applications) — see `docs/DEMO-ACCOUNTS.md`
- [ ] Deploy / demo environment (Docker on VPS or Railway)
- [ ] Background job queue (Redis or database driver) for video compression, reports, emails (REL-02)

---

## Track B — Employer

### Job Posting (POST-01–09) `#5, #6`
- [x] Posting create/edit form: title, rich-text description, skill chips, employment type, location, salary, accommodations provided checklist (POST-01, POST-06)
- [x] Exclusionary language detector: scan on save, highlight phrases with inclusive replacements (POST-02) `#5`
- [x] Accept / dismiss language suggestions before publish (POST-03)
- [x] Posting status machine: Draft → Pending Scan → Published → Closed → Archived (POST-04)
- [x] Employer posting management dashboard: list, edit, close, archive (POST-05)
- [ ] Applicant list per posting: ranked by fit score (POST-07)
- [ ] Update applicant status (Under Review → Interview → Hired / Rejected) with optional note (POST-08)
- [ ] On "Hired": prompt employer to confirm PWD-verified status; write `workforce_records` (POST-09)

### Workspace Accessibility Matrix (POST-06) `#6`
- [ ] Employer onboarding: categorized checklist of accommodations provided (physical, sensory, schedule, tech)
- [ ] Edit matrix from employer settings at any time

### Workforce & Tax Incentives (TAX-01–05) `#7`
- [ ] PWD Workforce Summary widget: total employees, PWD count, PWD % (TAX-01)
- [ ] RA 10524 deduction calculation display (TAX-02)
- [ ] Plain-language RA 10524 benefit explanation (TAX-04)
- [ ] Tax Incentive Report export: PDF + XLSX (TAX-03) — queued job, email when ready
- [ ] Workforce records table: add/remove employees, mark PWD-verified (TAX-05)

---

## Track C — Candidate

### Profile Building (PROF-01–10) `#1, #4`
- [x] Multi-step profile wizard with progress bar; each step saves independently (PROF-01)
- [ ] Auto-save draft every 60 seconds; restore on revisit (PROF-10, UX-09)
- [x] Skills tag/chip input with predictive autocomplete from skills taxonomy (PROF-03)
- [ ] Accessibility needs checklist (physical / sensory / schedule / tech); hard vs. soft toggle per item (PROF-04)
- [ ] Per-field visibility toggles: public / employers-only / hidden (PROF-06)
- [ ] Profile completion percentage bar + missing-section prompts (PROF-07)
- [ ] Résumé PDF upload (PROF-08)
- [ ] Voice input on all free-text fields (Web Speech API) with microphone toggle (PROF-02) `#1`
- [x] FSL video record / upload (max 5 min), attached to profile (PROF-05) `#4`
- [x] All free-text inputs wired to predictive text engine (PROF-09, ASSIST-01)

### Candidate Dashboard
- [x] Matched jobs list sorted by fit score (MATCH-06, MATCH-07)
- [ ] Bookmarked jobs list (MATCH-08)
- [x] Application history with statuses: Submitted, Under Review, Interview, Hired, Rejected (APP-06)
- [x] Withdraw pending application (APP-07)

---

## Track D — Matching & UI

### Accessibility & Display Toggles (A11Y, ASSIST-07–08) `#3`
- [ ] Global accessibility settings panel (persistent per user): high-contrast, large text (1.5×), dyslexia font, reduced motion, predictive text on/off (ASSIST-07)
- [ ] Settings panel reachable from every page via header control (ASSIST-08)
- [ ] CSS token implementation: `--color-contrast`, `--font-scale`, `--font-family-dyslexia` (A11Y-01–10)
- [ ] `prefers-reduced-motion` respected for all animations (A11Y-09)

### Predictive Text Engine (ASSIST-01–06) `New feature — interview finding #2`
- [x] In-browser word-prediction (custom vanilla JS): fires on all `<input type="text">` and `<textarea>` after 2+ chars (ASSIST-01)
- [x] Tab → accept top suggestion; Escape → dismiss (ASSIST-02)
- [x] Vocabulary: English common words + Filipino words + job-domain terms + user personal history (ASSIST-03)
- [x] Keyboard navigation (arrow keys) through suggestion dropdown; no focus steal (ASSIST-04)
- [x] ARIA: `role="listbox"`, `aria-activedescendant`, live region for screen readers (ASSIST-05)
- [x] Voice toggle button (microphone icon) on all free-text fields (ASSIST-06)

### Matching Service (MATCH-01–08) `#2`
- [ ] `AccessibilityFitService`: hard filter (exclude missing required accommodations) (MATCH-02)
- [ ] Weighted score: 60% accessibility fit + 40% skills match; store in `matches` (MATCH-01)
- [ ] Human-readable score explanation: "Matches X of Y needs" (MATCH-03)
- [ ] Job filters: job type, location, accommodation type, industry, minimum fit score (MATCH-04)
- [ ] Filters use chips / toggles / dropdowns only — no open text without predictive input (MATCH-05)

### Application Flow (APP-01–08)
- [ ] Single-page apply flow: pre-fill from profile, cover letter (voice + predictive), document upload (PWD ID, résumé, FSL video) (APP-01, APP-02)
- [ ] Gate submission if PWD ID not on file; show inline prompt (APP-03)
- [ ] On submit: on-screen confirmation + email with reference number (APP-05, NOTIF-01)
- [ ] Employer email notification on new application (APP-08, NOTIF-01)

### Accessibility QA
- [ ] WCAG 2.1 AA pass: semantic HTML, ARIA, keyboard nav, focus visible (A11Y-01–10)
- [ ] AT compatibility: NVDA + Firefox, JAWS + Chrome manual test (AT-01–06)
- [ ] No `tabindex > 0`; no focus traps; switch-access compatible (AT-03, AT-05)
- [ ] axe-core + Pa11y CI check on every PR (SYS-05)
- [ ] Final NVDA and axe-core passes (Nov 23–29)

### Testing & Docs
- [x] PHPStan Level 5 clean; Pint formatted (routes pass level 7; pint clean on changed files)
- [ ] Full-loop feature tests (Pest) for each user role
- [ ] Laravel Dusk browser tests for critical flows: register, apply, hire
- [ ] Documentation final update

---

## Checkpoints

| Date | Target |
|---|---|
| ~~Nov 4~~ | ~~Scaffold pushed~~ — _missed; login done, register in progress_ |
| Nov 8 | Verification, matrix, profile form wizard, layout merged |
| Nov 15 | Detector, FSL, voice, matching, auditor merged; check fallback order |
| **Nov 22** | **All features working end to end (thin)** |
| Nov 29 | Hardened, tested, demo-ready |
| **Dec 1** | **Deadline** |
