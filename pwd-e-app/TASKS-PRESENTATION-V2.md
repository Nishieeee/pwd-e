# TASKS-PRESENTATION-V2.md
# PWD-E Comprehensive Presentation & A11y Polish Task Suite

**Goal:** Deliver a **100% complete, end-to-end UI/UX experience** for the core user flow (**Register → Verify → Profile → Post → Match → Apply → Review/Interview → Hire → Workforce Record**) designed specifically for People with Disabilities (PWDs) and verified inclusive employers.  
**Standard:** Zero dead-ends, seamless edge-case and "what-if" handling, and uncompromising adherence to universal accessibility (WCAG 2.1 AA, RA 10524, RA 10173).

---

## 1. PWD-Type User-Friendliness & Assistive Design Matrix

All tasks below must fulfill accessibility requirements tailored to four core disability profiles:

| Disability Category | UX / A11y Requirement | Implementation Target |
|---|---|---|
| **Motor & Dexterity / Cerebral Palsy** | **Reduced-Typing UI:** Autocomplete, single-click options, predictive word completion, and voice-to-text. Generous touch targets ($\ge 48$px). Sticky actions to avoid long scroll journeys. | Single-page apply, profile builder, and search filters. |
| **Visual Impairments (Blind / Low-Vision)** | **AT & High-Contrast Compatibility:** Strict semantic markup (`main`, `nav`, `h1`-`h4`), ARIA live regions for dynamic updates, high-contrast mode toggle, font scaling (100%–150%), Atkinson Hyperlegible typography. | Layout headers, modal dialogs, status badges, application receipt. |
| **Hearing & Speech (Deaf / Hard-of-Hearing)** | **Visual First & FSL:** Video resume and application attachments with Filipino Sign Language (FSL) support. Zero reliance on audio indicators; visual confirmation toasts and status chips. | FSL video streaming, application review modal, status changes. |
| **Neurodivergent & Cognitive (Dyslexia / ADHD)** | **Low Cognitive Load & Clarity:** Dyslexia-friendly font toggle, step-by-step progress, predictable layout grids, clear undo actions, human-readable match explanations. | Match breakdown cards, job descriptions, form validations. |

---

## 2. Priority Checklist: Main Flow & Edge Cases

### Phase 1: Candidate Discovery & Match Transparency (P0)

- [ ] **1.1 Fix Public Directory Apply CTA (`/jobs`)**
  - **Logged-in Candidate:** Direct link to `/jobs/{job}/apply`.
  - **Guest User:** Redirect to `/login` with `intended` return URL back to the job application.
  - **Employer Account:** Disabled button or informative chip ("Logged in as Employer").
  - **Edge Case / What-If:** Job is unpublished or closed mid-browsing → show *"This posting is no longer accepting applications"* banner.
- [ ] **1.2 Unify Job Matches CTA (`/jobs/matches`)**
  - Replace the silent 1-click apply button with a clear *"Review & Apply"* button leading to the dedicated apply page.
  - Retain quick profile preview without submitting prematurely.
- [ ] **1.3 Human-Readable Match Explanation (MATCH-03)**
  - Replace raw percentage with explicit breakdown: *"100% Accessibility Fit (4 of 4 required accommodations provided: Ramp, Screen Reader, FSL, Flexible Hours)"* + *"85% Skills Fit"*.
  - **Edge Case / What-If:** Candidate has not set required accommodations → show prompt: *"Customize your accessibility needs in Profile to get precise match ratings"*.
- [ ] **1.4 Zero-Match Empty State**
  - When search or filter returns 0 jobs $\ge 85\%$ fit, render an accessible card offering:
    1. Clear filters button.
    2. Suggestion to adjust work arrangement or explore all verified inclusive employers.

---

### Phase 2: Single-Page Application Experience (P0)

- [ ] **2.1 Document Re-Use vs Upload (APP-01, APP-02)**
  - Pre-populate saved candidate documents:
    - `[x] Use profile résumé (resume_2026.pdf)` *(with option to upload replacement)*
    - `[x] Use verified PWD ID on file` *(with option to upload updated copy)*
    - `[x] Attach profile FSL video` *(with option to record/upload new clip)*
  - Visual preview badges showing document file name and size.
- [ ] **2.2 Assistive Cover Letter Editor (APP-02, ASSIST-01–06)**
  - Integrate predictive text word suggestions bar directly above or below the `<textarea>`.
  - Add Web Speech API microphone toggle (`Start Voice Dictation` / `Listening...` / `Stop`) with visual listening feedback.
  - Low-typing pre-set starter chips: *"I am excited to apply...", "My background in accessibility...", "I require the listed accommodations..."*.
- [ ] **2.3 Missing PWD ID Gate & Recovery (APP-03)**
  - If `$user->pwd_id_number` is missing, display an inline alert card with:
    - Reason: *"Philippine Law (RA 10524) requires a valid PWD ID to ensure inclusive employment accreditation."*
    - Direct action button: *"Add PWD ID to Profile"* that links to Step 2 of the Profile Wizard, preserving the current job ID in session so the user seamlessly returns to complete the application.
- [ ] **2.4 Duplicate Application Handling**
  - If candidate already applied, replace form with an informative status card:
    - Reference Number, Date Applied, and Current Status (`Under Review` / `Interview`).
    - Action button: *"Track in Applications"*.
- [ ] **2.5 Polished Confirmation Receipt (APP-05)**
  - On submission, render an accessible confirmation screen with:
    - Formatted reference code: `PWD-APP-2026-XXXXX`
    - Quick "Copy Reference" button with live screen-reader announcement.
    - Expected next steps timeline ("Employer review within 3-5 business days").
    - "Print / Save Receipt" button and link back to Applications Tracker.
  - Trigger queued employer notification email.

---

### Phase 3: Candidate Application Tracker (`/applications`) (P0)

- [ ] **3.1 Application History & Statuses (APP-06)**
  - Filter tabs: `All`, `Active`, `Interviews`, `Archived/Closed`.
  - Accessible status chips:
    - `Applied` (Blue)
    - `Under Review` (Amber)
    - `Interview Scheduled` (Purple)
    - `Hired` (Green)
    - `Not Selected` (Gray)
- [ ] **3.2 Upcoming Interview Banner & Modal**
  - For applications in `interview` status with date/time set:
    - Display interview schedule card (Date, Time, Location / Video Link, Requested Accommodations).
    - "Add to Calendar (.ics download)" action button.
- [ ] **3.3 Friendly Application Withdrawal (APP-07)**
  - Modal prompt: *"Are you sure you want to withdraw your application for [Job Title] at [Company]?"*
  - Non-punitive option: optional reason dropdown (e.g., "Accepted another offer", "Circumstances changed").
  - Immediately updates state and informs the candidate.

---

### Phase 4: Employer Job Creation & Inclusive Language (P0)

- [ ] **4.1 Interactive Inclusive Language Detector UX (POST-02–05)**
  - When typing description or requirements, scan against the discriminatory lexicon.
  - Display non-punitive recommendation chips:
    - *Detected:* `"physically fit"` → *Suggestion:* Click to replace with `"able to perform essential functions"`.
    - *Detected:* `"normal speech"` → *Suggestion:* Click to replace with `"effective communication skills"`.
- [ ] **4.2 Categorized Accommodations Checklist (POST-01)**
  - Group accommodations by category: *Mobility & Physical Access*, *Visual & Screen Reader*, *Hearing & Communication (FSL)*, *Work Flexibility*.
  - Pre-check accommodations already declared in the employer's profile matrix.
- [ ] **4.3 Remove Lingering `wire:navigate` (D-02 Compliance)**
  - Eliminate all `wire:navigate` attributes in `dashboard.blade.php` and employer layouts to ensure rock-solid plain-page accessibility.

---

### Phase 5: Employer Applicant Review & Hiring Workflow (P0)

- [ ] **5.1 Applicant Dashboard & Ranking (POST-07)**
  - List applicants per posting ranked by accessibility fit score.
  - Filter tabs: `All Applicants`, `Under Review`, `Shortlisted for Interview`, `Hired`.
- [ ] **5.2 Comprehensive Applicant Review Modal**
  - Detail view presenting:
    - Candidate bio and contact info.
    - Full match breakdown: accommodations needed vs provided.
    - Cover letter with comfortable reading typography.
    - Embedded résumé PDF viewer (via secure signed controller stream).
    - Embedded FSL video player with accessible controls.
  - **Edge Case / What-If:** Candidate has no FSL video or cover letter → render polite placeholder: *"No video résumé submitted; candidate qualified via credentials and résumé."*
- [ ] **5.3 Interview Scheduling Workflow (POST-08)**
  - When employer changes status to `Interview`:
    - Open modal: Select Interview Date/Time, Meeting Link / Office Room, and Special Accessibility Accommodations to prepare (e.g. FSL Interpreter requested).
    - Updates application record and triggers candidate notification.
- [ ] **5.4 Formal Hiring Confirmation & Tax Onboarding (POST-09)**
  - When employer selects `Hired`:
    - Open Hire Modal:
      - Confirm Start Date.
      - Confirm Annual/Monthly Wage (for statutory tax credit calculation).
      - PWD Verification check: *"Candidate has verified PWD ID [ID Number] on file under RA 10524."*
    - Writes `WorkforceRecord` and displays immediate statutory tax deduction incentive update.

---

### Phase 6: Workforce Management & RA 10524 Tax Incentives (`/employer/workforce`) (P0)

- [ ] **6.1 Tax Deductions Summary Widget (TAX-01, TAX-02)**
  - Display gross wage deductions: 25% of total wages paid to verified PWD employees.
  - Show compliant breakdown: Total Workforce, PWD Headcount, Percentage ($\ge 1\%$ DOLE target).
- [ ] **6.2 Workforce Table Actions**
  - List hired candidates with PWD ID status badge.
  - Safe toggle for PWD verification with tooltip explaining Bureau of Internal Revenue (BIR) audit compliance.
- [ ] **6.3 Empty State Guide**
  - If no employees hired yet: display educational guide on how hiring through PWD-E qualifies companies for tax deductions under RA 10524.

---

### Phase 7: Universal Accessibility & AT Polish (P1)

- [ ] **7.1 Accessibility Header Controls (ASSIST-07, ASSIST-08)**
  - High Contrast Toggle (125% contrast + pure background).
  - Text Size Scaler (A- / 100% / A+ up to 150%).
  - Dyslexia Font Toggle (Atkinson Hyperlegible / Lexend).
  - Screen Reader Announcements: verify all Livewire actions output to `#a11y-announcer`.
- [ ] **7.2 Reduced Motion Compliance**
  - Ensure all CSS transitions, Livewire loading spinners, and modals strictly respect `prefers-reduced-motion: reduce`.
- [ ] **7.3 Mobile-First Audit (320px–412px)**
  - Verify every button and interactive target is $\ge 48 \times 48$ px.
  - Prevent horizontal scroll; ensure all tables collapse gracefully into mobile card lists.
- [ ] **7.4 Screen Reader & Keyboard Walkthrough**
  - Full keyboard-only tab pass: zero focus traps, visible `:focus-visible` outlines.
  - NVDA / JAWS speech check on form labels and errors.

---

## 3. Presentation Day Demo Checklist

| Time Slice | Persona | Demo Scenario |
|---|---|---|
| **00:00–02:00** | Candidate | Profile with speech input & FSL video → browse matches → view match explanation → apply using pre-filled documents & predictive cover letter → receive reference receipt. |
| **02:00–04:00** | Employer | Review job applicants ranked by fit → preview applicant details (résumé & FSL video) → schedule interview with accommodations → hire candidate. |
| **04:00–05:00** | Employer / Tax | Navigate to Workforce & Tax → demonstrate 25% RA 10524 wage deduction calculation and BIR compliance data. |
| **05:00–06:00** | A11y Showcase | Switch high contrast, scale font to 150%, enable dyslexia font, demonstrate screen reader announcements and mobile responsive layout. |
