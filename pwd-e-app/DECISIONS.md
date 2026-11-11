# DECISIONS.md

"Accepted" = team confirmed. "Proposed" = working default awaiting team confirmation. "Superseded" = replaced by a later decision.

| ID | Decision | Status | Rationale |
|---|---|---|---|
| D-01 | Use Laravel as the backend framework | Accepted | Built-in auth, validation, storage, policies, queues; fastest path for the team |
| D-02 | Use Blade + Livewire + Alpine.js (not React/Inertia); keep plain page loads for navigation | Accepted | Server-rendered HTML gives a better default accessibility baseline (focus reset, title announcement) |
| D-03 | Single monolith, no separate API | Proposed | Scope is role-based CRUD, matching, reports, dashboards |
| D-04 | Rule-based weighted matching, not ML | Proposed | Explainable, testable, appropriate for a vulnerable user group |
| D-05 | Rule-based exclusionary language detector, optional LLM for rewrites only | Proposed | Phrases are easy to catch with a curated lexicon |
| D-06 | PostgreSQL as the database (local server: PostgreSQL 18, database `pwd_e`, role `pwd_e_user`) | Accepted | Relational data; team chose PostgreSQL over MySQL on 2026-11-02 |
| D-07 | Encrypt disability fields; per-field visibility controlled by the candidate | Proposed | Disability data is sensitive under RA 10173 (SEC-01, SEC-09) |
| D-08 | Web Speech API for the voice prototype; Whisper as upgrade path | Proposed | No cost for the prototype; Whisper is more accurate for Filipino/Taglish |
| D-09 | Core features due Dec 1, 2026; four parallel tracks aim for all features (thin) by Nov 22; fallback cut order in `ROADMAP.md` | Accepted | Hard deadline; parallel work leaves time for hardening |
| D-10 | Features ship as thin end-to-end versions first; polish after Dec 1 | Proposed | Keeps all modules achievable in time |
| D-11 | Predictive text / word-suggestion engine on all free-text inputs (Tab-completion, ARIA listbox) | Accepted | Explicitly requested by stakeholder (professor with cerebral palsy) — interview 2026-11-11; see ASSIST-01–06 in `docs/REQUIREMENTS.md` |
| D-12 | PWD ID upload is required at registration / first profile completion; applications are blocked until it is on file | Accepted | Stakeholder interview 2026-11-11: same application requirements as non-PWD candidates + PWD ID; see AUTH-09, APP-03 |
| D-13 | Employer RA 10524 tax incentive tracker is a first-class feature with PDF + XLSX export | Accepted | Stakeholder interview 2026-11-11: employers receive deductions/discounts per PWD worker; see TAX-01–05 |
| D-14 | All form UI must default to selection-based controls (chips, dropdowns, toggles, checkboxes) over free-text; free-text fields must have predictive text | Accepted | Stakeholder interview 2026-11-11: minimize typing for users with motor disabilities; see UX-01, ASSIST-01 |
| D-15 | Site must be compatible with external AT tools (NVDA, JAWS, switch access) — not just WCAG conformance; manual AT testing is required before Dec 1 | Accepted | Stakeholder interview 2026-11-11; see AT-01–06 in `docs/REQUIREMENTS.md` |

## Adding a decision
Add a row with the next ID, the decision, status, and a one-line rationale. Never delete old rows; mark them "Superseded by D-xx".
