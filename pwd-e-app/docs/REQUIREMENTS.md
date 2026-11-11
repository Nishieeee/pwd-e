# PWD-E — Software Requirements Specification (SRS)

**Version:** 1.1 — Updated after stakeholder interview (2026-11-11)
**Project:** PWD-E Job Portal · SE1, WMSU CCS

---

## Table of Contents

1. [System Overview](#1-system-overview)
2. [Stakeholder Interview Findings](#2-stakeholder-interview-findings)
3. [System Requirements](#3-system-requirements)
4. [Functional Requirements](#4-functional-requirements)
   - 4.1 Authentication & Registration
   - 4.2 Candidate Profile Building
   - 4.3 Job Discovery & Matching
   - 4.4 Job Application
   - 4.5 Employer: Job Posting
   - 4.6 Employer: Workforce & Tax Incentives
   - 4.7 Administration & Verification
   - 4.8 Predictive Text & Assistive Input
   - 4.9 Notifications
5. [Non-Functional Requirements](#5-non-functional-requirements)
   - 5.1 Accessibility (WCAG 2.1 AA)
   - 5.2 Assistive-Technology Compatibility
   - 5.3 Performance
   - 5.4 Security & Privacy
   - 5.5 Usability & Reduced-Typing Design
   - 5.6 Reliability & Availability
   - 5.7 Maintainability

---

## 1. System Overview

PWD-E is an accessibility-first, web-based job portal that connects People with Disabilities (PWDs) to employers whose workplaces can accommodate them. Beyond skills matching, the system evaluates workplace accessibility fit and surfaces the most compatible opportunities first. The system is governed by Philippine law — specifically **RA 10524** (PWD employment quota & tax incentives) and **RA 10173** (Data Privacy Act).

### Users & Roles

| Role | Description |
|---|---|
| **Candidate (PWD)** | Job seeker with a disability; builds profile, applies to jobs |
| **Employer** | Company or office; posts jobs, manages workforce records, tracks RA 10524 incentives |
| **Administrator (PESO/LGU)** | Verifies accounts, monitors analytics, reviews audit logs |

---

## 2. Stakeholder Interview Findings

> [!IMPORTANT]
> These findings were gathered from an interview with a professor who is a person with cerebral palsy, and directly informed the requirements below.

| # | Finding | Impact on Requirements |
|---|---|---|
| 1 | PWD applicants must meet **the same job requirements** as non-PWD candidates; the only additional document is a **PWD ID** | Application form must include a PWD ID upload field alongside standard supporting documents |
| 2 | **Predictive text / word suggestion** is a critical accessibility feature for users with cerebral palsy — typing a letter should surface word completions, similar to IDE tab-completion | Implement an in-browser predictive text engine on all free-text inputs |
| 3 | Forms must be **as low-typing as possible** — prefer dropdowns, toggles, chips, radio groups, voice input, and autocomplete over open text boxes | All form design must default to selection-based UI patterns |
| 4 | The site must be **compatible with external assistive-technology tools** (screen readers, switch access, eye-tracking, AAC devices) — not just WCAG conformance | Thorough ARIA semantics, no custom focus traps, test with NVDA/JAWS |
| 5 | Employers receive **tax deductions / discounts** for every PWD they employ (RA 10524) | Employer dashboard must track PWD headcount and generate a tax incentive report |

---

## 3. System Requirements

These are the hardware, software, and infrastructure requirements needed to operate PWD-E.

| ID | Requirement |
|---|---|
| **SYS-01** | The system shall run on a Linux-based server (or Docker container) with PHP 8.2+, Composer, Node.js 18+, and MySQL 8 / PostgreSQL 15+. |
| **SYS-02** | The system shall expose a single HTTPS endpoint; all HTTP requests redirect to HTTPS. |
| **SYS-03** | Private file storage (PWD IDs, FSL videos, business permits) shall not be publicly accessible; files are served via signed, time-limited URLs only. |
| **SYS-04** | The system shall support at least 100 concurrent users without degrading page-load times beyond the threshold in NFR-PERF-01. |
| **SYS-05** | The system shall run automated accessibility checks (axe-core / Pa11y) in CI for every pull request. |
| **SYS-06** | Backups of the database shall be performed daily and retained for 30 days. |

---

## 4. Functional Requirements

### 4.1 Authentication & Registration

| ID | Requirement |
|---|---|
| **AUTH-01** | The system shall provide registration for three roles: Candidate (PWD), Employer, and Administrator. |
| **AUTH-02** | Registration for Candidates shall collect: full name, email, password, disability type (dropdown/multi-select), and PWD ID number. |
| **AUTH-03** | Registration for Employers shall collect: company name, company email, password, and business permit number. |
| **AUTH-04** | All accounts shall remain inactive (login blocked) until an Administrator approves them. |
| **AUTH-05** | The system shall send an email notification to the applicant when their account is approved or rejected. |
| **AUTH-06** | The system shall implement email-verified password reset via a time-limited token link. |
| **AUTH-07** | Sessions shall expire after 2 hours of inactivity; the user is redirected to the login page with a friendly message. |
| **AUTH-08** | The login form shall validate email format in real-time (Livewire) and show inline error messages without a page reload. |
| **AUTH-09** | Candidates shall upload a scan/photo of their **PWD ID** during registration or at first profile completion; this document is required before job applications are submitted. |
| **AUTH-10** | The system shall support "Remember me" (30-day persistent session) with explicit opt-in. |

### 4.2 Candidate Profile Building

| ID | Requirement |
|---|---|
| **PROF-01** | A Candidate shall be able to build their profile using a **multi-step wizard** with a progress bar; each step can be saved independently. |
| **PROF-02** | The profile wizard shall allow input via voice (Web Speech API) on any free-text field, with a visual microphone toggle. |
| **PROF-03** | Skills shall be entered using a **tag/chip input with predictive autocomplete** drawn from a predefined skills taxonomy. |
| **PROF-04** | Accessibility needs (accommodations required) shall be selected from a categorized checklist (physical, sensory, schedule, tech); each item is marked hard (required) or soft (preferred). |
| **PROF-05** | Candidates shall be able to record or upload a **Filipino Sign Language (FSL) video résumé** (max 5 minutes). |
| **PROF-06** | Candidates shall control visibility of each profile field (public, employers-only, hidden) via inline toggle switches. |
| **PROF-07** | The profile shall display a **completion percentage bar** and prompt the user to fill in missing sections. |
| **PROF-08** | Candidates shall be able to upload a traditional résumé PDF alongside the structured profile. |
| **PROF-09** | All free-text fields in the profile (bio, headline, work experience) shall offer **predictive word suggestions** (see ASSIST-01). |
| **PROF-10** | The system shall auto-save draft profile data every 60 seconds to avoid data loss. |

### 4.3 Job Discovery & Matching

| ID | Requirement |
|---|---|
| **MATCH-01** | The system shall compute an **accessibility-fit score** per candidate–posting pair using: 60% accessibility fit + 40% skills match. |
| **MATCH-02** | Any posting that lacks an accommodation the candidate marked as *required* (hard) shall be excluded from their results entirely. |
| **MATCH-03** | Candidates shall see a human-readable explanation of their score (e.g., "Matches 5 of your 6 accessibility needs"). |
| **MATCH-04** | The jobs list shall support filtering by: job type, location, accommodation type, industry, and minimum fit score. |
| **MATCH-05** | Filters shall use toggles, chips, and dropdowns — no open-text filter boxes unless paired with predictive suggestions. |
| **MATCH-06** | The system shall display jobs sorted by total score descending by default. |
| **MATCH-07** | A job card shall show: title, employer, location, employment type, top-3 matched accommodations, and the total fit score. |
| **MATCH-08** | Candidates shall be able to save/bookmark jobs for later. |

### 4.4 Job Application

| ID | Requirement |
|---|---|
| **APP-01** | Candidates shall apply to a job via a single-page application flow; the system shall pre-fill data from their profile to minimize typing. |
| **APP-02** | The application form shall include fields for: cover letter (optional, voice-enabled, with predictive text), and document uploads (PWD ID, résumé PDF, FSL video — all optional unless the posting requires them). |
| **APP-03** | A Candidate's **PWD ID** must be on file (uploaded at registration or profile) before submission is allowed; the system shall show an inline prompt if it is missing. |
| **APP-04** | Candidates shall submit applications meeting the **same standard qualifications** as non-PWD applicants; the system shall not lower skill requirements. |
| **APP-05** | After submission the candidate receives an on-screen confirmation and email with the application reference number. |
| **APP-06** | Candidates shall view their application history with statuses: Submitted, Under Review, Interview, Hired, Rejected. |
| **APP-07** | Candidates shall be able to withdraw a pending application. |
| **APP-08** | Employers shall receive an email notification when a new application is submitted to their posting. |

### 4.5 Employer: Job Posting

| ID | Requirement |
|---|---|
| **POST-01** | Employers shall create job postings using a form-based editor with: title, description (rich-text), required skills (chip input), employment type, location, salary range (optional), and required accommodations provided. |
| **POST-02** | Upon saving, the system shall automatically scan the job description for **exclusionary language** and highlight flagged phrases with suggested inclusive replacements. |
| **POST-03** | Employers shall be able to accept or dismiss each language suggestion before publishing. |
| **POST-04** | Postings shall have statuses: Draft, Pending Scan, Published, Closed, Archived. |
| **POST-05** | Employers shall be able to view, edit, close, and archive their postings from a management dashboard. |
| **POST-06** | Each posting shall declare the **workplace accessibility accommodations available** using a categorized checklist (same taxonomy as candidate needs). |
| **POST-07** | Employers shall be able to view a ranked list of applicants for each posting with fit scores. |
| **POST-08** | Employers shall be able to update an applicant's status (Under Review → Interview → Hired / Rejected) with an optional note. |
| **POST-09** | When an employer marks an applicant as Hired, the system shall prompt them to confirm the applicant is a verified PWD for workforce-record purposes. |

### 4.6 Employer: Workforce & Tax Incentives

| ID | Requirement |
|---|---|
| **TAX-01** | The employer dashboard shall display a **PWD Workforce Summary**: total employees, number of verified PWD employees, and PWD workforce percentage. |
| **TAX-02** | The system shall calculate the employer's eligibility for **RA 10524 tax deductions/discounts** based on the number of verified PWD employees on record. |
| **TAX-03** | Employers shall be able to export a **Tax Incentive Report** (PDF and XLSX) listing each PWD employee, their disability category, hire date, and the deduction basis. |
| **TAX-04** | The system shall display a plain-language explanation of the RA 10524 benefit (e.g., "You may claim an additional 25% deduction on wages paid to X verified PWD employees"). |
| **TAX-05** | The workforce records table shall allow employers to add/remove employees and mark each as PWD-verified (linked to their verification request). |

### 4.7 Administration & Verification

| ID | Requirement |
|---|---|
| **ADMIN-01** | Administrators shall review and approve or reject account verification requests (PWD ID for candidates; business permit for employers) from a queue. |
| **ADMIN-02** | Administrators shall be able to view the submitted document image in the review panel without downloading it. |
| **ADMIN-03** | Administrators shall be able to flag a verification as requiring additional documents, triggering a notification to the user. |
| **ADMIN-04** | The admin dashboard shall display platform analytics: total users by role, applications per week, top employers, top skills demanded, and accessibility error rates. |
| **ADMIN-05** | Administrators shall access a full audit log (powered by spatie/laravel-activitylog) filterable by user, action type, and date range. |
| **ADMIN-06** | Administrators shall be able to suspend or reinstate any user account. |
| **ADMIN-07** | The system shall run and store accessibility audit reports (axe-core / Pa11y) on a schedule; Administrators view these in a dedicated panel. |
| **ADMIN-08** | Administrators shall be able to manage the exclusionary-language lexicon: add, edit, and remove terms and their suggested replacements. |

### 4.8 Predictive Text & Assistive Input

> [!IMPORTANT]
> This module was explicitly requested by the interviewed stakeholder (professor with cerebral palsy). It is a first-class feature, not an enhancement.

| ID | Requirement |
|---|---|
| **ASSIST-01** | Every free-text `<textarea>` and `<input type="text">` in the application shall display a **word-prediction dropdown** after the user types 2 or more characters of a word, suggesting up to 5 completions. |
| **ASSIST-02** | Pressing **Tab** shall accept the top suggestion (matching IDE tab-completion behavior); pressing **Escape** shall dismiss the dropdown. |
| **ASSIST-03** | The word prediction vocabulary shall include: common English words, Filipino words, job-domain terminology (titles, skills, industries), and words from the user's own previously accepted suggestions. |
| **ASSIST-04** | The prediction dropdown shall be keyboard-navigable (arrow keys cycle through suggestions) and shall not steal focus from the input field. |
| **ASSIST-05** | The prediction dropdown shall be announced by screen readers using an ARIA live region (`role="listbox"`, `aria-activedescendant`). |
| **ASSIST-06** | Voice input (Web Speech API) shall be available as an alternative input mode on all free-text fields; a microphone icon button toggles it on/off. |
| **ASSIST-07** | The system shall provide a **global accessibility settings panel** (persistent per user) with toggles for: high-contrast mode, large text (1.5× scale), dyslexia-friendly font (OpenDyslexic or similar), reduced motion, and predictive text on/off. |
| **ASSIST-08** | The accessibility settings panel shall be reachable from every page via a consistent, keyboard-accessible control in the page header. |

### 4.9 Notifications

| ID | Requirement |
|---|---|
| **NOTIF-01** | The system shall send email notifications for: account approved/rejected, application received (employer), application status changed (candidate), and verification document required. |
| **NOTIF-02** | In-app notifications shall appear in a bell icon in the header, showing unread count; clicking navigates to the relevant resource. |
| **NOTIF-03** | Users shall be able to manage notification preferences (email on/off per event type). |

---

## 5. Non-Functional Requirements

### 5.1 Accessibility (WCAG 2.1 AA)

| ID | Requirement |
|---|---|
| **A11Y-01** | All pages shall conform to **WCAG 2.1 Level AA** as verified by axe-core and Pa11y automated scans with zero critical or serious violations. |
| **A11Y-02** | All form controls shall have an associated `<label>` or `aria-label`; placeholder text shall not be the only label. |
| **A11Y-03** | All interactive elements shall be reachable and operable using keyboard alone (Tab, Shift+Tab, Enter, Space, Arrow keys). |
| **A11Y-04** | Focus indicators shall be clearly visible (minimum 3:1 contrast ratio against the adjacent background). |
| **A11Y-05** | Color shall not be the sole means of conveying information (icons, patterns, or text labels must accompany color indicators). |
| **A11Y-06** | All images shall have descriptive `alt` text; decorative images shall use `alt=""`. |
| **A11Y-07** | All video content shall have closed captions; FSL video résumés shall display a text description field alongside them. |
| **A11Y-08** | The page shall remain fully functional when text is zoomed to 200% without horizontal scrolling on a 1280px viewport. |
| **A11Y-09** | Animated or moving content shall respect `prefers-reduced-motion`; animations shall pause or be removed when this media query is set. |
| **A11Y-10** | Error messages shall be announced to screen readers using ARIA live regions and be associated with the specific control that caused the error. |

### 5.2 Assistive-Technology Compatibility

| ID | Requirement |
|---|---|
| **AT-01** | The system shall be fully operable with **NVDA + Firefox** and **JAWS + Chrome** screen-reader combinations (manual testing required). |
| **AT-02** | The system shall not break or interfere with browser-native zoom, text-spacing overrides, or Windows/macOS high-contrast mode. |
| **AT-03** | The system shall not use `tabindex` values greater than 0 (which breaks natural DOM order navigation). |
| **AT-04** | No ARIA roles shall override or conflict with native HTML semantics unnecessarily (follow "first rule of ARIA"). |
| **AT-05** | The system shall be compatible with switch-access navigation (sequential focus, no time-dependent interactions without extension). |
| **AT-06** | Third-party widgets (rich-text editor, date picker, file uploader) shall each be evaluated for screen-reader compatibility before adoption. |

### 5.3 Performance

| ID | Requirement |
|---|---|
| **PERF-01** | Pages shall load (Time to Interactive) in under **3 seconds** on a standard broadband connection. |
| **PERF-02** | Predictive text suggestions shall appear within **200 ms** of a keystroke. |
| **PERF-03** | Accessibility fit scores shall be computed or served from cache within **500 ms** of a page load. |
| **PERF-04** | File uploads (PWD ID, résumé, FSL video) shall show a progress indicator; large video uploads shall be chunked. |
| **PERF-05** | Report generation (PDF/XLSX) shall be queued as a background job; the user is notified by email and in-app when ready. |

### 5.4 Security & Privacy

| ID | Requirement |
|---|---|
| **SEC-01** | Disability-related fields (accessibility needs, disability type) shall be stored encrypted (Laravel encrypted casts). |
| **SEC-02** | PWD IDs, business permits, and FSL videos shall be stored on a private disk and served only via signed, expiring URLs (max 1-hour validity). |
| **SEC-03** | Every access to sensitive records (disability data, verification documents) shall be written to the audit log. |
| **SEC-04** | All forms shall include CSRF protection (Laravel default). |
| **SEC-05** | File uploads shall be validated for MIME type, file extension, and maximum size (images: 5 MB; PDFs: 10 MB; video: 200 MB) on the server side. |
| **SEC-06** | Passwords shall be hashed using bcrypt (cost ≥ 12); plain-text passwords shall never be stored or logged. |
| **SEC-07** | Rate limiting shall be applied to login attempts (5 per minute per IP), registration, and password reset endpoints. |
| **SEC-08** | The system shall not expose stack traces or internal error details in production responses. |
| **SEC-09** | The system shall comply with the **Data Privacy Act of 2012 (RA 10173)**: only the minimum necessary disability information shall be collected; candidates consent explicitly at registration. |

### 5.5 Usability & Reduced-Typing Design

| ID | Requirement |
|---|---|
| **UX-01** | Every form shall prefer selection-based controls (dropdowns, radio groups, checkboxes, chip selects, toggles) over open text fields wherever a finite option set exists. |
| **UX-02** | Candidate application forms shall pre-fill all fields available from the existing profile; the user edits only what differs. |
| **UX-03** | Multi-step forms shall show a persistent step indicator (progress bar) and allow back-navigation without data loss. |
| **UX-04** | Inline validation shall be used on all form fields; errors shall appear beside the relevant field without full-page reloads. |
| **UX-05** | Date pickers shall be keyboard-accessible and also accept manual date entry. |
| **UX-06** | The site shall be fully responsive and mobile-first; all features shall be accessible on screens 320 px wide and above. |
| **UX-07** | No critical action shall be achievable only by hover; all hover interactions shall have a touch/click equivalent. |
| **UX-08** | Destructive actions (withdraw application, delete posting) shall require a confirmation dialog before execution. |
| **UX-09** | The system shall auto-save form progress every 60 seconds and restore it on page revisit. |

### 5.6 Reliability & Availability

| ID | Requirement |
|---|---|
| **REL-01** | The system shall target **99% uptime** during the academic demonstration period. |
| **REL-02** | All background jobs (video compression, report generation, email sending) shall be queued and retried up to 3 times on failure. |
| **REL-03** | Database migrations shall be reversible (each migration includes a `down()` method). |
| **REL-04** | The application shall degrade gracefully when third-party services (Web Speech API, email provider) are unavailable — features fall back to manual input; errors are logged and surfaced to Administrators. |

### 5.7 Maintainability

| ID | Requirement |
|---|---|
| **MAINT-01** | All features shall have automated tests (Pest/PHPUnit for backend; Laravel Dusk for critical browser flows); coverage shall not drop below 80% on domain service classes. |
| **MAINT-02** | The codebase shall pass PHPStan Level 5 analysis and Laravel Pint formatting checks in CI. |
| **MAINT-03** | Every architectural decision shall be recorded in `DECISIONS.md` before implementation. |
| **MAINT-04** | Environment-specific configuration shall live exclusively in `.env`; no hard-coded credentials or URLs. |
| **MAINT-05** | The exclusionary-language lexicon and accommodations taxonomy shall be managed through database seeders and an admin UI — not hard-coded in application logic. |

---

## Cross-Reference: Interview Findings → Requirements

| Interview Finding | Requirement IDs |
|---|---|
| Same job requirements + PWD ID document | AUTH-09, APP-02, APP-03, APP-04 |
| Predictive text for cerebral palsy users | ASSIST-01, ASSIST-02, ASSIST-03, ASSIST-04, ASSIST-05, PERF-02 |
| Reduce typed forms / prefer selection UI | ASSIST-06, ASSIST-07, APP-01, UX-01, UX-02, PROF-03, MATCH-05 |
| Assistive-technology tool compatibility | AT-01 through AT-06, A11Y-01 through A11Y-10 |
| Employer tax deductions for PWD workers | TAX-01 through TAX-05 |
