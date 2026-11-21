# Demo Accounts

Seeded by `DatabaseSeeder` (run `php artisan migrate:fresh --seed` or `php artisan db:seed`).  
All accounts use the standard password: **`password`**.

---

## 1. System Administrator (PESO / LGU)
| Email | Name | Role | Access / Scope |
|---|---|---|---|
| `admin@pwd-e.local` | PWD-E Admin | Admin | Full platform access, PWD ID & business permit verifications, `/admin` dashboard |

---

## 2. Inclusive Candidates (PWD)
| Email | Name | Disability Category | Location | Focus / Credentials |
|---|---|---|---|---|
| `pagaroganjhonclein@gmail.com` | Jhon Clein Pagarogan | Motor / Physical | Zamboanga City (Ayala) | Software Engineer; Wheelchair Ramp, Restroom & Workstation needs |
| `maria.clara.santos@wmsu.edu.ph` | Maria Clara Santos | Physical / Motor | Zamboanga City (Tetuan) | WMSU Alumna, Frontend Dev; Voice input, Complete application history |
| `candidate1@pwd-e.local` | Maria Santos | Visual | Quezon City | Accessibility Tester; Screen reader & High-contrast display needs |
| `candidate2@pwd-e.local` | Jose Reyes | Mobility | Cebu City | Customer Support; Wheelchair ramp & accessible restroom needs |
| `candidate3@pwd-e.local` | Ana Cruz | Hearing | Davao City | FSL Interpreter; Filipino Sign Language & Live Captions needs |

---

## 3. Inclusive Employers & Companies
| Email | Company Name | Industry | Location | Seeded Postings |
|---|---|---|---|---|
| `employer@pwd-e.local` | Acme Accessibility PH Corp. | IT & Accessibility | Makati City | 10 accessible job postings |
| `employer2@pwd-e.local` | Inclusive Works Corp. | BPO & Inclusion | Cebu City | 10 accessible job postings |
| `contact@openpathdigital.ph` | OpenPath Digital PH | IT & Software | Zamboanga City / Remote | Accessible Web & QA roles |
| `careers@innovatetech.ph` | InnovateTech Solutions | Tech & Support | Zamboanga City / Hybrid | Digital Content & Data roles |
| `hr@zamboangamaritime.com` | Zamboanga Maritime Logistics | Logistics & Supply Chain | Zamboanga City | Documentation & Operations |
| `contact@accessworks.ph` | AccessWorks BPO | Business Services | Remote | Customer Support & FSL Chat |
| `careers@brightpathtech.ph` | BrightPath Technology | Software Development | Remote | Accessibility QA Tester |
| `jobs@kandolasolutions.ph` | Kandola Solutions | Outsourcing | Zamboanga City / Hybrid | Data & Virtual Assistant |
| `careers@inclusivestyles.ph` | InclusiveStyles Retail | Retail & E-Commerce | Hybrid | Inventory & Catalog Clerk |
| `hr@bayandigital.ph` | Bayan Digital Studios | Creative & Media | Remote | Graphic Design & Captions |
| `recruitment@aegisfinancial.ph` | Aegis Financial Services | Finance & Banking | Hybrid | Bookkeeping & Compliance |
| `admin@mindanaocommunity.org` | Mindanao Community NGO | Non-Profit / Community | Zamboanga City | Community Program Assistant |
| `careers@techforward.ph` | TechForward Philippines | Information Technology | Remote | Junior Web Developer |

---

## Quick-Fill on Sign In
The `/login` screen includes a 1-click **Demo Accounts** selector that automatically fills the email and password (`password`) for rapid testing and live presentation walkthroughs.
