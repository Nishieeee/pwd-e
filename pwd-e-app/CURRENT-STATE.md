# CURRENT-STATE.md

_Last updated: 2026-11-11_

## Phase
Implementation in progress. **Core features are due December 1, 2026.** 20 days remaining.

---

## Working ✅
- PostgreSQL 18 connected: local DB `pwd_e` (role `pwd_e_user`); core schema applied (revised ERD); seeders run (accommodations, lexicon, admin + roles).
- Home, Jobs, Employers, About pages (static Blade, mobile-first); reusable accessibility panel component.
- Auth: Fortify scaffold + redesigned login page (1:1 layout, mobile-first, live email validation, SVG icons, a11y error wiring).
- `docs/ERD.md` created; `docs/REQUIREMENTS.md` v1.1 (full SRS from stakeholder interview).

## Done — Docs & Decisions ✅
- Project proposal (Chapter 1) with features, roles, and workflow.
- Stack decision: Laravel + Blade + Livewire.
- Planning docs, four-track roadmap, `ROADMAP.md`, `TASKS.md`, `DECISIONS.md`.
- Stakeholder interview (2026-11-11): identified predictive text, reduced-typing UI, AT compatibility, tax incentives, PWD ID requirement → captured in `docs/REQUIREMENTS.md` and `DECISIONS.md` (D-11–D-15).

---

## Incomplete / Not Started 🔲

### Highest priority (blocker for all other tracks)
- Registration redesign: role picker + PWD ID field for candidates, business permit for employers (AUTH-01–03, AUTH-09)
- Verification admin queue (AUTH-04, ADMIN-01–03)

### Track A
- Email notifications (NOTIF-01)
- In-app notification bell (NOTIF-02)
- Notification preferences (NOTIF-03)
- Admin analytics dashboard (ADMIN-04)
- Audit log admin view (ADMIN-05)
- Suspend / reinstate user (ADMIN-06)
- Scheduled a11y audit panel (ADMIN-07)
- Lexicon management UI (ADMIN-08)
- Privacy hardening (encrypted casts, signed URLs, rate limiting) (SEC-01–09)

### Track B
- Job posting form + language detector (POST-01–09)
- Workspace accessibility matrix (POST-06)
- RA 10524 tax incentive tracker + export (TAX-01–05)

### Track C
- Profile wizard (all steps) (PROF-01–10)
- Voice input (PROF-02, ASSIST-06)
- FSL video record/upload (PROF-05)
- Candidate dashboard + application history (APP-06–07)

### Track D
- Predictive text engine (ASSIST-01–06) — **new, first-class feature**
- Global accessibility settings panel (ASSIST-07–08)
- Matching service wired to real data (MATCH-01–08)
- Application flow with PWD ID gate (APP-01–05)
- Full-loop feature tests + AT testing (NVDA/JAWS)

---

## Known Issues & Open Questions
- **Track ownership** still TBD — assign A/B/C/D before Nov 13.
- Registration redesign is a blocker; every other track depends on the user existing with a role.
- Matching weights (0.6 / 0.4) and score threshold are placeholders — confirm with real data once profiles exist.
- Predictive text library not yet chosen — evaluate `awesomplete` / `tribute.js` / custom Alpine.js component (see open question in `HANDOFF.md`).
- Notification queue driver not yet decided: database (simple) vs. Redis.
- Web Speech API works on Chromium only — form must function without it.
- LLM API for exclusionary language rewrite suggestions: still undecided; lexicon-based detection ships first.
