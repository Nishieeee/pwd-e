# ROADMAP-PRESENTATION.md

**Pivot (Nov 18):** Presentation/demo focus. Goal = **100% UI/UX on the main flow** (register → profile → post → match → apply → hire) working end-to-end and visually polished. Backend depth and advanced features are deferred.

Hard deadline unchanged: **December 1, 2026**.

---

## Main flow to demo (priority order)

1. **Register** (Candidate / Employer) — exists, polish mobile + a11y
2. **Verify** (admin queue) — exists
3. **Profile builder** (candidate) — exists (5-step), polish voice/FSL
4. **Job posting** (employer) — exists, polish language detector UX
5. **Job matches** (candidate) — exists, needs real match score + explanation
6. **Apply** (candidate) — partial: needs single-page apply, PWD ID gate, confirmation + reference #
7. **Applicant management** (employer) — list + status updates + hire flow
8. **Hire → workforce record** (employer) — POST-09

## UI/UX polish track

- Global accessibility settings panel (ASSIST-07–08): high-contrast, large text (1.5×), dyslexia font, reduced motion, predictive text toggle — persistent per user, reachable from header.
- WCAG 2.1 AA pass on all main-flow screens (axe-core + Pa11y).
- Mobile-first audit (320–412px): touch targets ≥44px, inputs ≥16px, no horizontal scroll.
- Display toggles CSS tokens (`--color-contrast`, `--font-scale`, `--font-family-dyslexia`).
- `prefers-reduced-motion` respected.
- NVDA/JAWS manual pass on the main flow.

## Deferred (post-presentation)

- RA 10524 tax incentive tracker/export (TAX-01–05)
- Workspace accessibility matrix (POST-06) — partial
- Predictive employment analytics (ADMIN-04 depth)
- Platform accessibility auditor panel (ADMIN-07)
- Auth extras (session expiry, remember-me, rate limiting hardening)
- Whisper/Python NLP upgrades
- Mobile app wrapper, push/SMS

## Daily plan (Nov 18–22)

| Day | Focus |
|---|---|
| Nov 18 | Apply flow single-page (pre-fill, cover letter, PWD ID gate, confirmation) + employer email |
| Nov 19 | Applicant management + hire flow → workforce_records |
| Nov 20 | Accessibility settings panel + CSS tokens + reduced motion |
| Nov 21 | WCAG/AT pass on main flow; mobile audit; axe-core CI |
| Nov 22 | Full end-to-end thin demo + NVDA pass |

## Rules (unchanged)
- Mobile-first, WCAG 2.1 AA, plain page loads (no `wire:navigate`), encrypt disability data, policies on routes, no new deps without `DECISIONS.md`.