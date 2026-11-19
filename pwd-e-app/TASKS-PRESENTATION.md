# TASKS-PRESENTATION.md

Presentation/demo focus: **100% UI/UX on the main flow** (register → profile → post → match → apply → hire).
Deadline: **December 1, 2026**. Thin demo target: **November 22, 2026**.
See `ROADMAP-PRESENTATION.md`.

---

## P0 — Main flow (must work end to end)

### Apply flow (APP-01–05)
- [ ] Single-page apply: pre-fill from profile, cover letter (voice + predictive), document upload (PWD ID, résumé, FSL video) (APP-01, APP-02)
- [ ] Gate submission if PWD ID not on file; show inline prompt (APP-03)
- [ ] On submit: on-screen confirmation + reference number (APP-05)
- [ ] Employer email notification on new application (APP-08, NOTIF-01)

### Applicant management (POST-07–09)
- [ ] Applicant list per posting: ranked by fit score (POST-07)
- [ ] Update applicant status (Under Review → Interview → Hired / Rejected) with optional note (POST-08)
- [ ] On "Hired": prompt employer to confirm PWD-verified status; write `workforce_records` (POST-09)

### Matching (MATCH-01–03)
- [ ] `AccessibilityFitService`: hard filter (exclude missing required accommodations) (MATCH-02)
- [ ] Weighted score: 60% accessibility fit + 40% skills match; store in `matches` (MATCH-01)
- [ ] Human-readable score explanation: "Matches X of Y needs" (MATCH-03)

---

## P1 — UI/UX polish

### Accessibility settings panel (ASSIST-07–08)
- [x] Global settings panel (persistent per user): high-contrast, large text (1.5×), dyslexia font, reduced motion, predictive text on/off
- [x] Reachable from every page via header control
- [x] CSS tokens: `--color-contrast`, `--font-scale`, `--font-family-dyslexia`
- [ ] `prefers-reduced-motion` respected for all animations

### Mobile-first audit
- [ ] All main-flow screens tested at 320–412px
- [ ] Touch targets ≥44px, inputs ≥16px, no horizontal scroll

### WCAG / AT pass
- [ ] axe-core + Pa11y on every main-flow page (zero critical/serious)
- [ ] NVDA + Firefox manual pass on main flow
- [ ] JAWS + Chrome manual pass on main flow
- [ ] No `tabindex > 0`, no focus traps, switch-access compatible

---

## P2 — Nice to have (if time)

- [ ] Caret-aware dropdown positioning for textareas (predictive text)
- [ ] Empty-state UI for no matches / no applications
- [ ] Employer applicant detail view
- [ ] Profile completion percentage bar (PROF-07)
- [ ] Per-field visibility toggles (PROF-06)

---

## Deferred (post-presentation)

- RA 10524 tax incentive tracker/export (TAX-01–05)
- Workspace accessibility matrix (POST-06)
- Predictive employment analytics (ADMIN-04 depth)
- Platform accessibility auditor panel (ADMIN-07)
- Auth extras (session expiry, remember-me, rate limiting)
- Whisper/Python NLP upgrades
- Mobile app wrapper, push/SMS

---

## Checkpoints

| Date | Target |
|---|---|
| Nov 18 | Apply flow single-page + employer email |
| Nov 19 | Applicant management + hire flow |
| Nov 20 | Accessibility settings panel + CSS tokens + reduced motion |
| Nov 21 | WCAG/AT pass + mobile audit + axe-core CI |
| **Nov 22** | **Full end-to-end thin demo + NVDA pass** |
| Nov 29 | Hardened, tested, demo-ready |
| **Dec 1** | **Deadline** |