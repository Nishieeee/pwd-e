# PROGRESS REPORT NO. 2

## 1. PROJECT INFORMATION
* **Resource / Key Personnel:** Diffinity (Group 6)
  1. PAGAROGAN, JHON CLEIN T. (Full-Stack Developer)
  2. JANDUGAN, JOVEL JOY L. (UI/UX Designer / Technical Writer)
  3. SINTOY, JOEBERT S. (Project Manager / Lead Researcher)
  4. BONGABONG, GOBERLYN E. (QA Lead / Tester)
* **Project Name:** PWD-E: An Accessibility-First Job Portal for Persons with Disabilities
* **Progress Report No.:** 2
* **Reporting Period:** November 18, 2026 to November 25, 2026
* **Progress Check:** Progress Check 2 (November 25, 2026 — Main Module Readiness)
* **GitHub Repository:** https://github.com/Nishieeee/pwd-e.git
* **Latest Git Commit:** `a81627e` (*feat: complete separation due process, reapplication pipeline, and UI/UX modal standardization*)

---

## 2. SUMMARY OF ACTIVITIES

| # | Activity / Deliverable | % Complete | Status | Results / Issues |
|---|---|---|---|---|
| 1 | **Stakeholder Consultation & Validation (PESO & DSWD)** | 90% | Completed | Conducted direct consultation with PESO and DSWD. Validated verification protocols: Medical Certificate serves as primary job qualification proof; PWD ID acts as secondary verification required for RA 10524 25% tax incentives. PESO confirmed willingness to pilot upon submission of an academic endorsement letter. Established that business legitimacy is under DTI/BIR jurisdiction. |
| 2 | **Statutory Due Process & Separation Contestation (R.A. 7277)** | 100% | Completed | Implemented end-to-end separation lifecycle: employer issues Notice of Separation, candidate receives statutory due process banner with DOLE rights on Dashboard, and can formally Acknowledge or Contest/Dispute. 2 new notifications, database migration, and test coverage completed. |
| 3 | **Candidate Reapplication & Pipeline Deduplication** | 100% | Completed | Allowed separated staff to reapply to open postings while locking active/disputed employees. Deduplicated employer applicant pipelines using `whereNotExists` subqueries to prevent obsolete records from cluttering recruiter tables. |
| 4 | **UI/UX Modal Standardization (Zero Browser Confirms)** | 100% | Completed | Replaced all native browser JavaScript `window.confirm()` popups across the entire codebase with custom, accessible Livewire/Alpine backdrop-blur modals (Position Filled job closure, invitation decline, workforce actions, job deletions, and lexicon management). |
| 5 | **Status Badge & Accessibility-Fit Alignment** | 100% | Completed | Standardized semantic color-coded status badges (`Offer Received`, `Offer Declined`, `Separated`, `Tech Ready`) across Candidate Tracker and Employer Applicants. Expanded tracker status dropdown filters to include `offered` and `hired`. |
| 6 | **Automated Feature Testing & Quality Assurance** | 95% | Completed | Built and verified comprehensive test suites: 18/18 passing in `ApplicantsWorkforceTest.php` (103 assertions) and 32/32 passing in Candidate portal tests (284 assertions) for a combined 50/50 test success rate. |
| 7 | **Compliance Matrix Documentation** | 100% | Completed | Authored `docs/COMPLIANCE_AND_VERIFICATION.md` detailing statutory alignments with R.A. 7277, R.A. 10524, R.A. 10070, R.A. 9442, R.A. 10173, R.A. 11106, and WCAG 2.1 Level AA, complete with 5 defense verification proofs. |
| 8 | **Chapter 6 – System Design & UI Compilation** | 80% | In Progress | Finalized high-fidelity UI screens, modal specifications, and schema definitions into `SE1_FinalDocumentation_G6.docx`. Visual DFD/UML compilation in progress. |
| 9 | **Overall Project Readiness** | 85% | On Track | Proof-of-concept backend and interactive frontend modules exceed Progress Check 2 milestones. Fully prepared for Final Defense (Dec 1–4, 2026). |

---

## 3. SECTION 1 — ACTIVITIES AND PROGRESS REPORT

### Activity 1: Institutional Stakeholder Consultation (PESO & DSWD)
* **Related Chapter / Deliverable:** Chapter 1 (Project Overview), Chapter 5 (Requirements Engineering), & Section 5 (Client Feedback)
* **Description:** The team conducted targeted institutional interviews with representatives from the Public Employment Service Office (PESO) and the Department of Social Welfare and Development (DSWD) to evaluate the operational feasibility of PWD-E's administrative workflows.
* **Expected Output:** Concrete operational guidelines regarding PWD credential validation, business legitimacy checks, and formal institutional pilot mechanisms.
* **Actual Output:** 
  1. *Medical Certificate as Primary Occupational Proof:* Clarified that for job placement, clinical fitness and accommodation evaluation rely primarily on the Medical Certificate / Physician's Assessment, while the physical PWD ID serves as secondary identity verification.
  2. *Tax Incentive Anchor (R.A. 10524):* Confirmed that while candidates can be placed with medical clearance, the physical PWD ID number remains strictly mandatory for employers claiming the 25% gross wage tax deduction with the BIR.
  3. *Business Legitimacy Clarification:* PESO clarified that verifying corporate legitimacy falls outside their mandate and is governed by DTI (sole proprietorships), SEC (corporations), and BIR (TIN Form 2303).
  4. *Institutional Handover:* PESO confirmed readiness to adopt the platform as an administrative facility upon submission of a formal academic endorsement letter from the university.
* **Percentage Complete:** 100%
* **Status:** Completed

### Activity 2: Statutory Employment Due Process & Contestation Module (R.A. 7277)
* **Related Chapter / Deliverable:** Chapter 5 (FR-27, FR-28) & Main Module Implementation
* **Description:** Built statutory security of tenure workflows ensuring PWD workers cannot be arbitrarily discharged. When an employer initiates employment separation in the Workforce module, the system transitions the record into `pending_review` and delivers an official notification. The candidate's dashboard displays an actionable due process card with R.A. 7277 legal advisories, enabling them to either **Confirm & Acknowledge** or **Contest/Dispute** for administrative mediation.
* **Expected Output:** Working interactive separation due process UI with email/database notifications and audit logging.
* **Actual Output:** Implemented `EmploymentSeparationNotice`, `EmploymentSeparationResponse`, database migration `add_separation_due_process_to_workforce_records_table.php`, and reactive Livewire components in `Workforce.php` and `Dashboard.php`.
* **Percentage Complete:** 100%
* **Status:** Completed

### Activity 3: Candidate Reapplication & Pipeline Deduplication
* **Related Chapter / Deliverable:** Chapter 5 (FR-19, FR-20) & Main Module Implementation
* **Description:** Resolved workflow ambiguities regarding former staff reapplying to previous employers. Candidates with acknowledged separation records can reapply to published openings (creating a new application record referencing `reapplied_from_id`), while active or disputed employees remain blocked. In the employer pipeline, deduplication logic (`whereNotExists`) ensures only the latest active submission is displayed with a `Former Staff / Rehire` indicator.
* **Expected Output:** Working reapplication authorization logic with deduplicated recruiter tables.
* **Actual Output:** Updated `Apply.php`, `JobMatches.php`, and `Applicants.php`. Verified via 18 automated tests in `ApplicantsWorkforceTest.php`.
* **Percentage Complete:** 100%
* **Status:** Completed

### Activity 4: UI/UX Modal Standardization & WCAG Polish
* **Related Chapter / Deliverable:** Chapter 6 (System Design & UI/UX Specifications)
* **Description:** Eliminated all native JavaScript `window.confirm()` popups across the entire codebase to achieve a presentation-grade, accessible user experience. Built unified Alpine/Livewire dialog modals with backdrop blur, keyboard dismissability (`Escape`), focus containment, and high-contrast color hierarchy.
* **Expected Output:** Zero occurrences of `wire:confirm` across all Blade templates.
* **Actual Output:** Audited and replaced browser dialogs across 5 major views:
  - Employer Applicants: Position Filled job closure modal
  - Candidate Dashboard: Invitation decline & separation acknowledgment modals
  - Employer Workforce: Unified confirm modal for notice cancellation, reactivation, and deletion
  - Employer Jobs Index: Job closure & draft deletion modal
  - Admin Lexicon: Exclusionary term deletion modal
* **Percentage Complete:** 100%
* **Status:** Completed

---

## 4. SECTION 2 — TASKS FOR NEXT REPORTING PERIOD (FINAL SPRINT AHEAD OF DEFENSE)

| # | Task Description | Expected Output | Person Responsible | Target Deadline |
|---|---|---|---|---|
| 1 | **Draft Formal Academic Endorsement Letter to PESO** | Formal university-endorsed letter requesting PESO partnership for capstone pilot implementation. | Joebert S. Sintoy (PM) & Jovel Joy L. Jandugan | Nov 27, 2026 |
| 2 | **Admin KYC Adaptation for DTI / BIR Business Accreditation** | Updated Employer verification queue reflecting DTI/SEC/BIR Form 2303 document checks based on PESO feedback. | Jhon Clein T. Pagarogan (Dev) | Nov 28, 2026 |
| 3 | **Compile Final Manuscript (SE1_FinalDocumentation_G6.docx)** | Complete Chapter 6 diagrams (ERD, Architecture, DFDs) and append Client Consultation minutes. | All Members (Lead: Jovel Joy L. Jandugan) | Nov 29, 2026 |
| 4 | **Seed Synthetic Demo Data & Finalize Presentation Slide Deck** | Comprehensive seeded demo data, finalized PowerPoint presentation, and live demonstration walkthrough script. | All Members (Lead: Goberlyn E. Bongabong) | Nov 30, 2026 |

---

## 5. SECTION 3 — CLIENT / STAKEHOLDER FEEDBACK

### Stakeholder Consultations:
1. **Public Employment Service Office (PESO)**
   - *Representative:* PESO Employment Facilitation Officer
   - *Key Insights:*
     - PESO confirmed that medical certificates serve as primary occupational evaluation for job placement; PWD IDs serve as secondary proof.
     - PESO confirmed they are open to piloting the system provided a formal university endorsement letter is submitted.
     - PESO clarified they do not verify business legitimacy; business registration is governed by DTI/SEC and BIR.
2. **Department of Social Welfare and Development (DSWD)**
   - *Representative:* Persons with Disability Focal Person
   - *Key Insights:*
     - Emphasized that R.A. 10524's 25% gross wage tax incentive strictly requires verified PWD ID credentials under BIR revenue regulations.
     - Recommended ensuring due process so PWD workers are protected from arbitrary dismissals.

### Requested Changes & Actions Taken:

| # | Stakeholder Recommendation | Action Taken in System | Priority | Status |
|---|---|---|---|---|
| 1 | Distinguish occupational fitness (medical cert) from statutory tax credit qualification (PWD ID). | Modeled multi-tier verification: candidates can access jobs with medical clearance, but R.A. 10524 tax credits unlock only upon verified PWD ID card. | High | Completed |
| 2 | Relieve PESO of business legitimacy liability. | Restructured employer onboarding to utilize standard commercial KYC: DTI BNRS, SEC registration, and BIR Form 2303 lookup. | High | Completed |
| 3 | Submit formal endorsement letter for PESO adoption. | Task assigned to Project Manager to coordinate with Computer Science department chair for formal endorsement letter. | High | In Progress |

---

## 6. PROGRESS REPORT SUMMARY

| Area | Current Status (PR 1) | Current Status (PR 2) | Progress Delta |
| :--- | :--- | :--- | :--- |
| **Documentation** | 70% | 85% | +15% |
| **Requirements Engineering** | 75% | 95% | +20% |
| **System Design** | 55% | 80% | +25% |
| **Main Module Implementation** | 50% | 90% | +40% |
| **Prototype & Automated Tests** | 75% | 95% | +20% |
| **Stakeholder Consultation & Validation** | 25% | 85% | +60% |
| **Overall Project Progress** | **58%** | **88%** | **+30%** |

### Current Project Status: ☑ ON TRACK

#### Reason for Current Status:
1. **Major Technical Milestones Completed:** The application backend and frontend are virtually complete ahead of schedule. All core hiring, accommodation matching, separation due process, and tax incentive workflows are fully functional with 50 passing automated tests.
2. **Stakeholder Grounding Achieved:** Consultations with PESO and DSWD validated the real-world operational assumptions of the platform and established a clear pathway for institutional adoption via an academic endorsement letter.
3. **Defense-Ready Quality:** Eliminated all native browser alert popups, standardized modal interactions, and established an exhaustive regulatory compliance reference in `docs/COMPLIANCE_AND_VERIFICATION.md`.

---

**PREPARED BY (DIFFINITY - GROUP 6):**
1. PAGAROGAN, JHON CLEIN T.
2. JANDUGAN, JOVEL JOY L.
3. SINTOY, JOEBERT S.
4. BONGABONG, GOBERLYN E.

*Date: November 25, 2026*

**NOTED BY:**
*Software Engineering Instructors:*  
Engr. Marjorie Rojas / Mr. Vin Czar R. Jailani  
*Date: ________________________*
