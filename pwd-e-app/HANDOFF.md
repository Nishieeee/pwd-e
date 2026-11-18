# HANDOFF.md

## Last session
- **Date:** 2026-11-18 — branch `dev`
- **Done:**
  - Employer applicants page: full applicant detail modal (profile, cover letter, skills, experience, education, résumé PDF, FSL video), status update, hire → workforce record.
  - Workforce & Tax page: summary widget, RA 10524 deduction display, records table (PWD-verified toggle, remove).
  - ApplicantDocumentController streams private résumé/FSL files gated by employer ownership.
  - TASKS.md ticked: POST-07–09, TAX-01/02/04/05.
  - 97 tests passing; Pint clean.

## State of the code
- Employer flow end-to-end: post job → view applicants → review full details → update status → hire → workforce record + RA 10524 deduction.
- Candidate flow: profile builder, job matches, apply (cover letter + docs), applications tracker.
- Admin: dashboard, users, lexicon, audit-log, verification queue, notifications.
- Schema and models complete. Seeders run.

## Next concrete action
**UI/UX polish:** mobile-first audit (320–412px), WCAG/AT pass (axe-core, NVDA), empty states, `prefers-reduced-motion`. See `TASKS-PRESENTATION.md` P1.

## Open questions
- Track ownership: who owns A, B, C, D? (Still TBD)
- Notification queue driver: database (simple) vs Redis (faster).

## Notes for the next session
- Keep navigation as plain page loads (no `wire:navigate`) — D-02.
- Livewire views must have a **single root element** (no `<x-layouts::app>` wrapper — layout is applied automatically for route components).
- App is light-only; do not reintroduce dark-mode support without updating the design tokens.
- Update `CURRENT-STATE.md`, `TASKS.md`, and `HANDOFF.md` at end of every session.