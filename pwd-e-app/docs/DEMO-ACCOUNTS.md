# Demo Accounts

Seeded by `DatabaseSeeder` (run `php artisan migrate:fresh --seed` or `php artisan db:seed`).
All accounts use password **`password`**.

## Admin
| Email | Name | Notes |
|---|---|---|
| `admin@pwd-e.local` | PWD-E Admin | Full admin access; `/admin` dashboard |

## Employer
| Email | Name | Company | Status |
|---|---|---|---|
| `employer@pwd-e.local` | Jane Doe | Acme Accessibility PH Corp. | Verified & active; accessibility matrix complete |

Seeded data: 2 job postings (1 published, 1 draft).

## Candidates
| Email | Name | Disability type | Headline | Location |
|---|---|---|---|---|
| `candidate1@pwd-e.local` | Maria Santos | Visual | Accessibility Tester | Quezon City |
| `candidate2@pwd-e.local` | Jose Reyes | Mobility | Customer Support Specialist | Cebu City |
| `candidate3@pwd-e.local` | Ana Cruz | Hearing | FSL Interpreter | Davao City |

Seeded data: candidate1 has 1 application (status: applied) to the published job.

## Notes
- All users are verified/active except where noted.
- Roles assigned via Spatie Permission (`candidate`, `employer`, `admin`).
- Re-running seeders is idempotent (`firstOrCreate`).