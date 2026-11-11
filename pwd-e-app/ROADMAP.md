# ROADMAP.md

**Hard deadline: core features working by December 1, 2026.**
_Today is November 11 — 20 days remaining._
_Updated 2026-11-11 to reflect stakeholder interview findings (see `docs/REQUIREMENTS.md` v1.1)._

---

## Strategy

Four team members own one track each and work in parallel. The goal is **all features working in thin form by Nov 22**, leaving the final week for hardening, accessibility testing, and bug fixes.

> [!IMPORTANT]
> Two new feature areas were added from the stakeholder interview:
> - **Predictive Text Engine** (ASSIST-01–06) — word suggestions on all free-text inputs, Tab-completion, screen-reader ARIA (Track D)
> - **Tax Incentive Reporting** (TAX-01–05) — RA 10524 deduction tracker and export (Track B)
>
> These are **first-class features**, not stretch goals.

---

## Tracks

| Track | Owner | Modules & Features |
|---|---|---|
| **A. Foundation & Admin** | _TBD_ | Auth & registration (AUTH-01–10), verification queue (#9), admin dashboard + analytics (#8), accessibility auditor (#10), notifications, security hardening |
| **B. Employer** | _TBD_ | Accessibility matrix (#6), job posting + language detector (#5), applicant management, hire flow, **RA 10524 tax incentive tracker (#7)** |
| **C. Candidate** | _TBD_ | Profile wizard (#1 voice, #4 FSL video, PROF-01–10), candidate dashboard, application history |
| **D. Matching & UI** | _TBD_ | Display toggles (#3), **predictive text engine (ASSIST-01–06)**, matching service (#2), application flow, WCAG / AT testing |

---

## Weekly Plan (Revised)

| Week | Dates | A. Foundation / Admin | B. Employer | C. Candidate | D. Matching / UI |
|---|---|---|---|---|---|
| ~~1~~ | ~~Nov 2–8~~ | ~~Scaffold, auth, roles, verification queue~~ → _login done; register + verification in progress_ | ~~Matrix migrations, models, onboarding form~~ | ~~Profile form skeleton~~ | ~~Base layout, display toggles~~ |
| **2** | **Nov 9–15** | Registration redesign (role picker, PWD ID field); verification admin queue; email notifications | Posting editor; exclusionary language detector; applicant list | Profile wizard steps: skills chips, accessibility needs checklist, visibility toggles; résumé upload | Matching service wired to real data; ranked results page; predictive text engine (alpha) |
| **3** | **Nov 16–22** | Analytics dashboard; demo data seeders; audit log view; suspend/reinstate | Hire flow → `workforce_records`; RA 10524 tracker + PDF/XLSX export | FSL video record/upload; voice input; candidate dashboard + application status | Full-loop integration; predictive text keyboard/ARIA; first axe-core + NVDA pass |
| **4** | **Nov 23–29** | Privacy hardening (encrypted casts, signed URLs, policies); deploy / demo setup | Bug fixes; RA 10524 report accuracy checks; lexicon UI | Bug fixes; accessibility fixes; FSL/voice polish | End-to-end tests; second AT pass (NVDA + JAWS); documentation final update |
| Buffer | **Nov 30–Dec 1** | Bug fixes only — freeze — rehearse demo | | | |

---

## Milestones

| Date | Milestone | Status |
|---|---|---|
| Nov 4 | Scaffold pushed; roles and auth working | ⚠️ Partial — login done, register pending |
| Nov 8 | Verification, matrix, profile form, layout merged | ⚠️ In progress |
| **Nov 15** | Language detector, FSL, voice, matching, auditor merged; predictive text alpha | 🔲 Upcoming |
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
