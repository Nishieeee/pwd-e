# ARCHITECTURE.md

## Style
A single Laravel monolith, server-rendered, organized by role and domain. No separate API or SPA.

## Layers
| Layer | Location | Responsibility |
|---|---|---|
| Routes and middleware | `routes/`, `app/Http/Middleware` | Role checks, verified-account gating |
| Controllers | `app/Http/Controllers/{Candidate,Employer,Admin}` | Thin: validate, call a service, return a view |
| Livewire components | `app/Livewire` | Interactive UI (profile builder, posting editor, dashboards) |
| Services | `app/Services` | All business logic |
| Policies | `app/Policies` | Authorization, including per-field candidate visibility |
| Models | `app/Models` | Eloquent models, encrypted casts, relationships |
| Jobs | `app/Jobs` | Video compression, report generation |
| Views | `resources/views` | Semantic Blade templates |

## Service modules
- `Matching/` – `AccessibilityFitService`, `SkillsMatchService`, `MatchScorer`
- `LanguageScan/` – `ExclusionaryLanguageDetector` (lexicon + regex; optional LLM rewrite)
- `Compliance/` – `Ra10524ReportService`
- `Analytics/` – hiring trends and simple forecasts
- `Verification/` – PWD ID and business permit review workflow
- `Audit/` – accessibility log ingestion

## Dependency rules
- Controllers and Livewire components may call Services and Models. Services may call Models and other Services.
- Services never import from Controllers or Livewire.
- External calls (speech-to-text, LLM) go through one wrapper class each so they can be swapped or faked in tests.

## Key flows
1. **Matching:** posting published or profile updated -> hard filter -> weighted score -> stored in `matches` -> candidate sees ranked postings above threshold.
2. **Posting publish:** draft -> language scan -> employer revises flagged phrases -> publish.
3. **Verification:** user uploads document (private disk) -> admin reviews -> `verified_at` set -> account active.
4. **Compliance report:** hire recorded in `workforce_records` -> report service computes PWD share -> PDF/XLSX export.

## Storage and security boundaries
- Documents and FSL videos on a private disk, served by signed, expiring URLs.
- Disability fields use encrypted casts.
- Candidate visibility settings are enforced in Policies and query scopes, not in the UI.

## Frontend boundary
Navigation is full page loads. Livewire handles in-page updates only. Alpine.js is for small client-side behavior (theme toggles, dropdowns).
