# ROADMAP.md

**Hard deadline: core features working by December 1, 2026.**
_Today is November 11 — 20 days remaining._
_Updated 2026-11-11 to reflect stakeholder interview findings (see `docs/REQUIREMENTS.md` v1.1)._

---

## Strategy

All four tracks run **in parallel**. The goal is **all features working in thin form by Nov 22**, leaving the final week for hardening, accessibility testing, and bug fixes.

> [!IMPORTANT]
> Two new feature areas were added from the stakeholder interview:
> - **Predictive Text Engine** (ASSIST-01–06) — word suggestions on all free-text inputs, Tab-completion, screen-reader ARIA (Track D)
> - **Tax Incentive Reporting** (TAX-01–05) — RA 10524 deduction tracker and export (Track B)
>
> These are **first-class features**, not stretch goals.

---

## Parallel Execution — How it Works

Track A has a **3–4 day critical path** (registration → gate → verification) that the other tracks depend on for end-to-end testing. While A lands that, B/C/D are **not idle** — they build skeletons, service classes, and unit tests against factory data.

```
Nov 11–13  Person A → Registration redesign + unverified gate  ← critical path
           Person B → Job posting form skeleton + migrations
           Person C → Profile wizard skeleton + service class stubs
           Person D → Matching service unit tests (factory data) + predictive text spike

Nov 14+    All four tracks go fully parallel against real registered users
Nov 22     D runs full-loop integration once B + C are merged
```

> [!NOTE]
> B, C, and D should **never wait idle** for A. Build the UI, write the service class, write unit tests with factories. Wire to real data once A's registration merges.

---

## Tracks

| Track | Owner | Modules & Features |
|---|---|---|
| **A. Foundation & Admin** | _TBD_ | Auth & registration (AUTH-01–10), verification queue (#9), admin dashboard + analytics (#8), accessibility auditor (#10), notifications, security hardening |
| **B. Employer** | _TBD_ | Accessibility matrix (#6), job posting + language detector (#5), applicant management, hire flow, **RA 10524 tax incentive tracker (#7)** |
| **C. Candidate** | _TBD_ | Profile wizard (#1 voice, #4 FSL video, PROF-01–10), candidate dashboard, application history |
| **D. Matching & UI** | _TBD_ | Display toggles (#3), **predictive text engine (ASSIST-01–06)**, matching service (#2), application flow, WCAG / AT testing |

---

## Daily Plan (Nov 11–22)

| Day | A. Foundation / Admin | B. Employer | C. Candidate | D. Matching / UI |
|---|---|---|---|---|
| **Nov 11–13** ⚡ | **Registration redesign** (role picker, PWD ID field, employer permit); unverified account gate middleware | Job posting form skeleton (Livewire component, migrations) | Profile wizard skeleton (steps scaffold, models, auto-save) | Matching service unit tests on factory data; predictive text library spike |
| **Nov 14–15** | Verification admin queue (list, approve/reject, email trigger) | Exclusionary language detector (lexicon + regex, live flag UI) | Profile wizard: skills chips, accessibility needs checklist, visibility toggles | Matching service wired to real users; ranked results page |
| **Nov 16–17** | Email notifications (Mailable jobs, queue); in-app bell | Applicant list per posting (ranked by score); status updates | Résumé PDF upload; voice input (Web Speech API toggle) | Predictive text: keyboard nav, Tab-completion, ARIA listbox |
| **Nov 18–19** | Analytics dashboard; audit log view; suspend/reinstate | Hire flow → `workforce_records`; RA 10524 deduction tracker | FSL video record/upload; candidate dashboard | Application flow (pre-fill from profile, PWD ID gate, confirmation email) |
| **Nov 20–22** | Demo data seeders; privacy hardening (encrypted casts, signed URLs) | RA 10524 PDF/XLSX export; lexicon management UI | Application history + withdraw; accessibility/voice fixes | Full-loop integration tests; first axe-core + NVDA pass |

---

## Milestones

| Date | Milestone | Status |
|---|---|---|
| Nov 4 | Scaffold pushed; roles and auth working | ⚠️ Partial — login done, register pending |
| **Nov 13** | Registration + unverified gate merged — B/C/D go fully parallel | 🔲 |
| **Nov 15** | Language detector, profile wizard, matching service, predictive text alpha all merged | 🔲 Upcoming |
| **Nov 22** | **All features working end to end (thin)** | 🔲 Target |
| Nov 29 | Hardened, tested, demo-ready | 🔲 |
| **Dec 1** | **Deadline** | 🔲 |

---

## Feature List (Full)

| # | Feature | Track | Req IDs | Status |
|---|---|---|---|---|
| 1 | Voice-activated profile builder | C | PROF-02, ASSIST-06 | 🔲 |
| 2 | Accessibility-fit matching | D | MATCH-01–08 | 🔲 |
| 3 | WCAG 2.1 AA + display toggles | D | A11Y-01–10, ASSIST-07–08 | 🔲 |
| 4 | FSL video résumés | C | PROF-05 | 🔲 |
| 5 | Exclusionary language detector | B | POST-02–03 | 🔲 |
| 6 | Workspace accessibility matrix | B | POST-06 | 🔲 |
| 7 | RA 10524 tax incentive tracker | B | TAX-01–05 | 🔲 |
| 8 | Predictive employment analytics | A | ADMIN-04 | 🔲 |
| 9 | Account verification engine | A | AUTH-04, ADMIN-01–03 | 🔲 |
| 10 | Platform accessibility auditor | A | ADMIN-07, SYS-05 | 🔲 |
| **New** | **Predictive text / assistive input** | **D** | **ASSIST-01–06** | 🔲 |
| **New** | **Tax incentive reporting** | **B** | **TAX-01–05** | 🔲 |

---

## Rules to Protect the Deadline

- Track A ships the registration redesign by **Nov 13**; until then others branch off current `develop`.
- Merge to `develop` at least every 2 days. Do not leave integration for the last week.
- Shared interfaces to agree on early: `accommodations` table, `MatchScorer` contract, `PredictiveTextService` interface, role names.
- **Never cut:** accessibility (WCAG), privacy (RA 10173), the core loop (register → profile → post → match → apply → hire).

## Fallback if Behind (check Nov 15)

Cut in this order if needed:
1. `#8` analytics (admin-only, can be a stub)
2. `#10` auditor panel (CI check still runs; just no admin view)
3. Polish on `#7` RA 10524 (keep deduction calculation; drop XLSX export)
4. `#1` Voice input (keep predictive text; drop microphone)

**Never cut:** verification, accessibility matrix, job postings, profile wizard, matching, applications, predictive text core (Tab-completion).

---

## Later / Post-Dec 1

- Whisper-based speech-to-text for Filipino/Taglish accuracy
- Python NLP for the exclusionary language detector
- Mobile app wrapper (PWA or React Native)
- Push notifications / SMS
- LLM-powered rewrite suggestions for exclusionary language
