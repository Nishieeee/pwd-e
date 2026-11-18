# HANDOFF.md

## Last session
- **Date:** 2026-11-17 — branch `feature/predictive-text-engine` (committed)
- **Done:**
  - Predictive text engine shipped (ASSIST-01–06): vanilla JS auto-attaching to all text inputs/textareas; Tab/Escape/arrows; ARIA listbox + live region; voice toggle (Web Speech API); vocabulary (English+Filipino+job-domain) + localStorage personal history (D-17).
  - Dark-mode forced to light (partials/head) to fix white-on-white; `/dashboard` redirects admins to `/admin`.
  - AUTH-05 confirmed already implemented via `VerificationStatusUpdated`.
  - JobPostingFlowTest added (11 tests); full suite 77 passing; Pint clean.

## State of the code
- Login, registration, verification gate + queue, admin dashboard/users/lexicon/audit-log, notifications (mail + bell), predictive text engine working.
- Schema and models complete. Seeders run.

## Next concrete action
**Application flow single-page (APP-01–05)** — pre-fill from profile, cover letter (voice + predictive), PWD ID gate, confirmation + reference number, employer email. See `ROADMAP-PRESENTATION.md`.

## Open questions
- Track ownership: who owns A, B, C, D? (Still TBD)
- Notification queue driver: database (simple) vs Redis (faster).

## Notes for the next session
- Keep navigation as plain page loads (no `wire:navigate`) — D-02.
- Livewire views must have a **single root element** (no `<x-layouts::app>` wrapper — layout is applied automatically for route components).
- App is light-only; do not reintroduce dark-mode support without updating the design tokens.
- Predictive text auto-attaches to all `<input type="text">` and `<textarea>`; no markup changes needed for new forms.
- Update `CURRENT-STATE.md`, `TASKS.md`, and `HANDOFF.md` at end of every session.