# HANDOFF.md

## Last session
- **Date:** 2026-11-16 — branch `dev`
- **Done:**
  - Verification admin queue shipped: `/admin/verifications` Livewire component with filter tabs (pending / needs-more-docs / approved / rejected / all), inline document preview via private-disk stream route, approve / reject / request-more-docs actions that update `verification_requests` + `users.status`/`verified_at` and fire `VerificationStatusUpdated` notification.
  - Added `needs_more_docs` status to `verification_requests.status` enum (migration; create migration updated for sqlite tests).
  - All 47 tests pass; Pint clean on changed files.

## State of the code
- Login, registration, verification gate, pending-notice page, verification admin queue working.
- Schema and models complete for profile/tax/notifications modules. Seeders run.
- Static pages: Home, Jobs, Employers, About + accessibility panel.

## Next concrete action
**Email notification system** (NOTIF-01) / **Auth extras** (AUTH-05 password reset email, AUTH-06, AUTH-07, AUTH-10) — or pick next Track A item. See `TASKS.md`.

## Open questions
- Track ownership: who owns A, B, C, D? (Still TBD)
- Predictive text implementation: custom Alpine.js vs library.
- Notification queue driver: database vs Redis.

## Notes for the next session
- Keep navigation as plain page loads (no `wire:navigate`) — D-02.
- Update `CURRENT-STATE.md`, `TASKS.md`, and `HANDOFF.md` at end of every session.
- Read `docs/REQUIREMENTS.md` before building any new feature.