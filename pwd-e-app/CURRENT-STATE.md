# CURRENT-STATE.md

_Last updated: 2026-11-02_

## Phase
Planning is complete; implementation starts now. **Core features are due December 1, 2026.** All ten features are targeted for Nov 22 using four parallel tracks.

## Working
- PostgreSQL 18 connected: local DB `pwd_e` (role `pwd_e_user`), base migrations (users, cache, jobs, spatie permissions) applied. D-06 accepted; domain migrations pending per `DATABASE.md`.

## Done (documentation and decisions)
- Project proposal (Chapter 1) with features, roles, and workflow
- Stack decision: Laravel with Blade + Livewire
- Planning docs, scope tiers, and a four-week plan (`ROADMAP.md`)

## Incomplete / not started
- Everything in the weekly plan, starting with scaffold, auth, roles, and migrations

## Known issues and open questions
- Scope risk: ten features in about three weeks of build time depends on all four members working in parallel and on the scaffold landing by Nov 4. Check progress on Nov 15 and use the fallback order in `ROADMAP.md`. Confirm what the instructors count as "core".
- Database engine (MySQL vs PostgreSQL) not final.
- Speech-to-text for the prototype will be the Web Speech API (Chromium browsers only); the form must work without it.
- Whether to use an LLM API for rewrite suggestions is undecided; the lexicon-based detector comes first.
- Matching weights (0.6 / 0.4) and the score threshold are placeholders until tested with sample data.
