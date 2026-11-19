# CURRENT-STATE.md

_Last updated: 2026-11-18 — branch `dev`_

## Phase
Implementation in progress. **Core demo features target: November 22, 2026. Hard deadline: December 1, 2026.** 13 days remaining.

---

## Working ✅
- **Public & Discovery Flow**: Role-aware "Apply" button on `/jobs` directory (candidates route to apply, guests to login with intended redirect, employers show badge); salary fallback displayed when omitted.
- **Job Matches & Recommendation**: Transparent match fit percentage with accommodation indicators; unified CTA pointing to the single-page application flow.
- **Single-Page Application Flow (`/jobs/{job}/apply`)**:
  - Pre-filled verified profile credentials (saved PDF resume checkbox, verified PWD ID on file, FSL video intro).
  - Assistive Cover Letter: low-typing starter phrases chips + Web Speech API voice dictation toggle + in-browser predictive text completion.
  - PWD ID gate under RA 10524: warning banner with direct recovery link to Profile wizard step 4.
  - Duplicate detection: displays existing reference number and status badge.
  - Submission receipt: formatted reference number (`PWD-APP-2026-XXXXX`), one-click clipboard copy with a11y announcement, print receipt action, next steps timeline, and employer notification dispatch.
- **Employer Flow (`/employer/jobs/{job}/applicants`)**:
  - Filter tabs by application status (`All`, `New`, `Under Review`, `Interviews`, `Hired`, `Declined`).
  - Applicant review modal with private streaming for PDF resume and FSL video introduction.
  - Interview scheduling modal: date/time, virtual meeting URL, requested accessibility accommodations, candidate notes.
  - Formal hire modal: start date, gross wage confirmation, and automated RA 10524 25% tax credit workforce record creation.
- **Workforce & Statutory Tax Incentives (`/employer/workforce`)**:
  - 25% gross wage deduction calculator under RA 10524.
  - PWD-verified compliance toggle with BIR audit safeguards.
- **Universal Accessibility Settings**:
  - High contrast (125% contrast + pure background), text scale (100%–150%), dyslexia font (Atkinson Hyperlegible), screen reader polite announcements (`#a11y-announcer`), predictive text toggle.
- **Profile Builder Step Validation & Accessible Feedback**:
  - Implemented step validation (`validateStep()`) inside `ProfileBuilder.php` on both `nextStep()` and `goToStep()`, preventing users from advancing past Step 2 if required fields (`firstName`, `lastName`, `email`, `phone`, `city`, `highestEducation`) are incomplete.
  - Added visual required indicators (`*`), `aria-required`, `aria-invalid`, high-visibility red error outlines, and screen-reader announced `@error` directives to all Step 2 input fields in `profile-builder.blade.php`.
  - Added top error alert banner on Step 2 summarizing missing required fields.
- **Settings Layout & Theme Unification**:
  - Encapsulated Settings within candidate/employer portal shells dynamically based on authenticated role (`layouts.candidate`, `layouts.employer`).
  - Wrapped settings forms within cohesive card containers matching design tokens, eliminating raw Laravel starter kit styling.
  - Cleared all `wire:navigate` usage from sidebar, layout, and nav components (Rule D-02 compliance).
- **Production Parity & Compliance Verification**:
  - Added official printable BIR RA 10524 Statutory Tax Certification summary in [workforce.blade.php](file:///C:/Users/ay/Projects/pwd-e/pwd-e-app/resources/views/livewire/employer/workforce.blade.php).
  - Added multi-state verification recovery handling (`pending`, `needs_more_docs`, `rejected`) in [verification-pending.blade.php](file:///C:/Users/ay/Projects/pwd-e/pwd-e-app/resources/views/auth/verification-pending.blade.php).
  - Fixed SQL like escaping bug in [PublicListing.php](file:///C:/Users/ay/Projects/pwd-e/pwd-e-app/app/Livewire/Jobs/PublicListing.php).
  - Removed remaining `wire:navigate` instances in employer job views and desktop user menu for strict D-02 plain-page navigation compliance.
  - Added exhaustive integration test suite in [ComponentIntegrityTest.php](file:///C:/Users/ay/Projects/pwd-e/pwd-e-app/tests/Feature/ComponentIntegrityTest.php).
- **Tests & Quality**: 108/108 Pest tests passing, 13/13 JS predictive text tests passing, Tailwind v4 / Vite asset build clean.

## Done — Docs & Decisions ✅
- Project proposal (Chapter 1) with features, roles, and workflow.
- Stack decision: Laravel + Blade + Livewire.
- Planning docs, four-track roadmap, `ROADMAP.md`, `TASKS.md`, `DECISIONS.md`.
- Stakeholder interview (2026-11-11): identified predictive text, reduced-typing UI, AT compatibility, tax incentives, PWD ID requirement → captured in `docs/REQUIREMENTS.md` and `DECISIONS.md` (D-11–D-15).

---

## Incomplete / Not Started 🔲

### Highest priority (blocker for all other tracks)
- ~~Registration redesign: role picker + PWD ID field for candidates, business permit for employers (AUTH-01–03, AUTH-09)~~ ✅
- Verification admin queue (AUTH-04, ADMIN-01–03) — ✅ shipped (list + filter tabs + approve/reject/request-more-docs + notify)

### Track A
- Email notifications (NOTIF-01) — ✅ shipped (queued mail + database channel)
- In-app notification bell (NOTIF-02) — ✅ shipped
- Notification preferences (NOTIF-03) — deferred
- Admin analytics dashboard (ADMIN-04) — ✅ partial shipped (role counts, apps/week, top employers; a11y error rate pending)
- Audit log admin view (ADMIN-05) — ✅ shipped
- Suspend / reinstate user (ADMIN-06) — ✅ shipped
- Scheduled a11y audit panel (ADMIN-07) — deferred (panel placeholder)
- Lexicon management UI (ADMIN-08) — ✅ shipped
- Privacy hardening (encrypted casts, signed URLs, rate limiting) (SEC-01–09)

### Track B
- Job posting form + language detector (POST-01–05) — ✅ shipped
- Applicant management + hire flow (POST-07–09) — ✅ shipped
- Accessibility matrix (POST-06) — not started
- RA 10524 tax tracker (TAX-01, 02, 04, 05) — ✅ shipped (export deferred)

### Track C
- Profile wizard (all steps) (PROF-01–10) — ✅ shipped (5-step wizard, PSGC zip auto-fill, resume PDF, FSL/PWD ID cards)
- Voice input (PROF-02, ASSIST-06) — predictive text engine covers text; Web Speech API toggle present
- FSL video record/upload (PROF-05) — ✅ shipped
- Candidate dashboard + application history (APP-06–07) — ✅ shipped

### Track D
- Predictive text engine (ASSIST-01–06) — ✅ shipped
- Global accessibility settings panel (ASSIST-07–08)
- Matching service wired to real data (MATCH-01–08) — partial (job matches uses stored fit scores)
- Application flow with PWD ID gate (APP-01–05) — partial (applyForJob persists application)
- Full-loop feature tests + AT testing (NVDA/JAWS)

---

## Known Issues & Open Questions
- **Track ownership** still TBD — assign A/B/C/D before Nov 13.
- Verification admin queue is the next blocker: pending accounts cannot be approved until it exists.
- Matching weights (0.6 / 0.4) and score threshold are placeholders — confirm with real data once profiles exist.
- Predictive text library not yet chosen — evaluate `awesomplete` / `tribute.js` / custom Alpine.js component (see open question in `HANDOFF.md`).
- Notification queue driver not yet decided: database (simple) vs. Redis.
- Web Speech API works on Chromium only — form must function without it.
- LLM API for exclusionary language rewrite suggestions: still undecided; lexicon-based detection ships first.
