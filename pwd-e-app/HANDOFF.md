# HANDOFF.md

## Last session
- **Date:** 2026-11-16 — branch `feature/verification-admin-queue`
- **Done:**
  - Verification admin queue shipped: `/admin/verifications` with filter tabs, document preview, approve/reject/request-more-docs + notifications; `needs_more_docs` status added.
  - Admin dashboard built (based on employer dashboard design): `/admin` analytics, `/admin/users` (suspend/reinstate), `/admin/lexicon` (exclusionary-term CRUD), `/admin/audit-log` (filterable, D-16).
  - Admin nav added to sidebar; activity logging wired into verification, suspension, lexicon actions.
  - Notifications: NOTIF-01 (queued mail + database classes: account approved/rejected, application received, application status changed, verification document required) + NOTIF-02 in-app bell with unread count; NOTIF-03 preferences deferred.
  - Dark-mode forced to light (partials/head) to fix white-on-white; `/dashboard` redirects admins to `/admin`.
  - 66 tests passing; Pint clean on changed files.

## State of the code
- Login, registration, verification gate + queue, admin dashboard/users/lexicon/audit-log, notifications (mail + bell) working.
- Schema and models complete. Seeders run.

## Next concrete action
**Job posting form + language detector (POST-01–09)** (Track B) or **profile wizard (PROF-01–10)** (Track C). See `TASKS.md`.

## Open questions
- Track ownership: who owns A, B, C, D? (Still TBD)
- Predictive text implementation: custom Alpine.js vs library.
- Notification queue driver: database (simple) vs Redis (faster).

## Notes for the next session
- Keep navigation as plain page loads (no `wire:navigate`) — D-02.
- Livewire views must have a **single root element** (no `<x-layouts::app>` wrapper — layout is applied automatically for route components).
- App is light-only; do not reintroduce dark-mode support without updating the design tokens.
- Update `CURRENT-STATE.md`, `TASKS.md`, and `HANDOFF.md` at end of every session.