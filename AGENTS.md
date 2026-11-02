# AGENTS.md

Instructions for any AI coding agent working on PWD-E. Read this file first, every session.

## Read order
1. `HANDOFF.md` – where the last session stopped and the next action
2. `CURRENT-STATE.md` – what works, what doesn't
3. `PROJECT.md` – scope, users, features, stack
4. `ARCHITECTURE.md`, `DATABASE.md`, `CONVENTIONS.md` – before writing code
5. `DECISIONS.md` – before proposing anything that changes the stack or design
6. `TESTING.md` – before declaring a task done
7. `TASKS.md` – pick the next task

## Non-negotiable rules
- **Accessibility first.** Every UI change must meet WCAG 2.1 AA: semantic HTML, labelled controls, visible focus, keyboard operability. Content that updates without a page load needs an `aria-live` region.
- **Plain page loads for navigation.** Do not enable `wire:navigate` or add SPA-style routing (see DECISIONS D-02).
- **Disability data is sensitive** (RA 10173). Encrypt it, respect per-field visibility, never log it in plain text, never expose it in URLs or error messages.
- **Business logic lives in `app/Services/`.** Controllers and Livewire components stay thin.
- **Authorize with Policies.** Never rely on hidden UI to protect data.
- **No new dependencies** without noting them in `DECISIONS.md`.
- **Never commit secrets** or real PWD IDs, permits, or personal data. Use seeders and factories for demo data.
- **Ask before** changing the database schema, the matching formula, or the stack.

## Deadline and scope
Core features are due **December 1, 2026**. Four tracks (A Foundation/Admin, B Employer, C Candidate, D Matching/UI) work in parallel; all ten features are targeted for Nov 22 in thin form. Stay inside your assigned track, merge to `develop` often, and prefer the simplest working implementation. Follow the fallback order in `ROADMAP.md` if behind. Never cut accessibility or privacy rules to save time.

## Stack at a glance
Laravel 11, PHP 8.2+, MySQL, Blade + Livewire + Alpine.js, Tailwind CSS, Pest/PHPUnit.

## Definition of done
Feature works for its role, has tests, passes `./vendor/bin/pint`, and shows no new axe-core violations.

## End of every session
Update `CURRENT-STATE.md`, `TASKS.md`, and `HANDOFF.md`. Record any new decision in `DECISIONS.md`.
