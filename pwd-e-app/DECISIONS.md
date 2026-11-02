# DECISIONS.md

| ID | Decision | Status | Rationale |
|---|---|---|---|
| D-01 | Use Laravel as the backend framework | Accepted | Built-in auth, validation, storage, policies, queues; fastest path for the team |
| D-02 | Use Blade + Livewire + Alpine.js (not React/Inertia); keep plain page loads for navigation | Accepted | Server-rendered HTML gives a better default accessibility baseline (focus reset, title announcement) |
| D-03 | Single monolith, no separate API | Proposed | Scope is role-based CRUD, matching, reports, dashboards |
| D-04 | Rule-based weighted matching, not ML | Proposed | Explainable, testable, appropriate for a vulnerable user group |
| D-05 | Rule-based exclusionary language detector, optional LLM for rewrites only | Proposed | Phrases are easy to catch with a curated lexicon |
| D-06 | MySQL as the database | Proposed | Relational data; team familiarity. PostgreSQL is acceptable |
| D-07 | Encrypt disability fields; per-field visibility controlled by the candidate | Proposed | Disability data is sensitive under RA 10173 |
| D-08 | Web Speech API for the voice prototype; Whisper as upgrade path | Proposed | No cost for the prototype; Whisper is more accurate |
| D-09 | Core features due Dec 1, 2026; four parallel tracks aim to finish all ten features (thin) by Nov 22, with the last week for hardening; fallback cut order in `ROADMAP.md` | Accepted | Hard deadline; parallel work completes more modules and leaves time for testing |
| D-10 | Features ship as thin end-to-end versions first; polish after Dec 1 | Proposed | Keeps all modules achievable in the time |

"Accepted" means the team confirmed it. "Proposed" means a working default awaiting team confirmation.

## Adding a decision
Add a row with the next ID, the decision, status, and a one-line rationale. Never delete old rows; mark them "Superseded by D-xx".
