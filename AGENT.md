# Project Operating Guide: pwd-e

## 1. Local Runtime & Commands
- **Working Directory:** `pwd-e-app`
- **Dev Server:** `composer run dev` or `php artisan serve` & `npm run dev`
- **Unit / Contract Tests:** `php artisan test` (Pest)
- **JS Tests:** `npm run test:js`
- **Lint / Static Analysis:** `vendor/bin/phpstan analyse`

## 2. Architecture & Conventions
- **Tier 3 Vault Specification:** [[projects/side-projects/pwd-e.md]]
- **Code Standards:** Laravel 11/13 + Livewire starter kit modular structure; zero speculative abstractions.
- **Verification Rule:** Every code modification must be verified with runtime tests before task completion.
