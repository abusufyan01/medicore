**HMS Backend**

Phase-wise Milestone and Task Breakdown — hms-backend

_Laravel 11 · MySQL 8 · Sanctum · Pest · Build order for one developer_

| **Property**           | **Detail**                                                                                                                                                               |
| ---------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| **System Name**        | HMS — Hospital Management System (single hospital)                                                                                                                       |
| **This File**          | Backend milestones and tasks for the hms-backend folder. Use it together with the HMS Backend Documentation file in the same folder.                                     |
| **Companion File**     | HMS Frontend Milestone and Task Breakdown (hms-frontend folder)                                                                                                          |
| **Phases**             | 12 phases (Phase 0 to Phase 11); Phase 10 (Lab) is optional                                                                                                              |
| **Tasks In This File** | 61 tasks: 47 backend tasks and 14 shared (Both) tasks                                                                                                                    |
| **Effort**             | 138 developer-days of backend work plus 38 days of shared work (169 days without Lab). Shared tasks appear in both files but belong to the project once.                 |
| **Task IDs**           | IDs such as P1-T3 are the same in both milestone files and in Section 26 of both documentation files, so a gap in the numbers is a task that belongs to the other folder |
| **Status**             | Version 1.0 — estimates are planning assumptions; re-estimate at the start of each phase                                                                                 |

**1\. How to Use This File**

Work one phase at a time. Inside a phase follow the build order, finish each task's "Done when" check, and start the next phase only when every item in the phase gate is checked. Because you build alone, the combined order tells you when to switch to the frontend folder.

**1.1 Reading the Tables**

| **Item**              | **Meaning**                                                                                                                                      |
| --------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------ |
| **#**                 | Global milestone number (M-1 to M-93), the same in both files and in the Milestone Plan.                                                         |
| **Task ID**           | P1-T3 means Phase 1, Task 3. Numbers restart in every phase and are shared by both folders.                                                      |
| **Layer and Who**     | Backend (Laravel Dev), Frontend (React Dev) or Both. You do all of them; the columns show which folder the work happens in.                      |
| **Pairs with**        | "Used by" lists the frontend tasks that call this backend work. Finish and document the endpoint (OpenAPI) before starting those frontend tasks. |
| **Est. Days**         | Developer-days for one developer, including tests.                                                                                               |
| **Steps / Done when** | The actions that make up the task, in order, and the single check that proves it works.                                                          |
| **Build order**       | The order for this folder only, and the combined order across both folders (B = backend, F = frontend, X = shared work).                         |

**1.2 Working Rules for This Folder**

- Follow the loop for every task: migration, model, policy, failing tests, service, request, controller, resource, route, events and audit, OpenAPI.
- Business rules (BR-xx), permissions and status names come from the HMS Backend Documentation (Sections 8, 9, 10, 12, 14). Change the documentation first if a decision changes.
- Write the OpenAPI entry in the same pull request as the endpoint, so the frontend folder can build against it.
- Test every endpoint as each role (allowed and forbidden) and as a guest.
- Use fake data only on local and staging; keep secrets in environment variables and commit only a sanitised .env.example.
- Each task ends as one merged pull request with CI green.

**1.3 Phase Overview**

| **Phase** | **Focus**                                        | **Tasks** | **Backend days** | **Shared days** | **Task IDs in this file**       | **Depends on**      |
| --------- | ------------------------------------------------ | --------- | ---------------- | --------------- | ------------------------------- | ------------------- |
| **P0**    | Setup and Project Baseline                       | 4         | 5                | 1               | T1, T2, T3, T6                  | None                |
| **P1**    | Foundation — Auth, Roles, Settings, Audit        | 8         | 21               | 4               | T1, T2, T3, T4, T5, T6, T7, T12 | Phase 0             |
| **P2**    | Patients                                         | 4         | 7                | 2               | T1, T2, T3, T7                  | Phase 1             |
| **P3**    | Doctors and Schedules                            | 5         | 9                | 2               | T1, T2, T3, T4, T8              | Phase 1             |
| **P4**    | Billing, Payments and Expenses                   | 8         | 20               | 3               | T1, T2, T3, T4, T5, T6, T7, T12 | Phase 2             |
| **P5**    | Appointments                                     | 6         | 15               | 4               | T1, T2, T3, T4, T5, T10         | Phase 3 and Phase 4 |
| **P6**    | Consultations, Prescriptions and Medical Records | 6         | 18               | 4               | T1, T2, T3, T4, T5, T10         | Phase 5             |
| **P7**    | Admissions, Wards and Beds                       | 4         | 10               | 3               | T1, T2, T3, T7                  | Phase 4             |
| **P8**    | Notifications                                    | 4         | 8                | 2               | T1, T2, T3, T5                  | Phase 5             |
| **P9**    | Reports and Dashboards                           | 4         | 10               | 2               | T1, T2, T3, T6                  | Phases 4 to 7       |
| **P10**   | Lab Module (Optional)                            | 2         | 5                | 2               | T1, T3                          | Phases 4 and 6      |
| **P11**   | Hardening, UAT and Release                       | 6         | 10               | 9               | T1, T2, T3, T5, T6, T7          | All earlier phases  |

_Shared days belong to both folders (integration, QA, release). Phase 10 is optional._

SECTION 2

**Phase-wise Milestones and Tasks**

**2\. Phase-wise Milestones and Tasks**

Each phase shows its goal, the build order, a task list in the Development Order layout, the steps and "Done when" check for every task, and the phase gate.

**Phase 0 — Setup and Project Baseline**

| **Goal**                 | Two repositories, CI and a deployed empty skeleton so every later phase ships the same way. |
| ------------------------ | ------------------------------------------------------------------------------------------- |
| **Covers**               | Documentation Sections 13, 23, 24                                                           |
| **Depends on**           | None                                                                                        |
| **Effort here**          | 5 backend days + 1 shared days = 6 developer-days · 4 tasks                                 |
| **Order (this folder)**  | T1 > T2 > T3 > T6                                                                           |
| **Order (both folders)** | B1 > B2 > B3 > F4 > F5 > X6 (B backend, F frontend, X shared)                               |

**Task list**

| **#** | **Task ID** | **Task**                 | **Layer** | **Who**     | **Est. Days** | **Pairs with**    |
| ----- | ----------- | ------------------------ | --------- | ----------- | ------------- | ----------------- |
| 1     | P0-T1       | Laravel backend setup    | Backend   | Laravel Dev | 2             | Backend only      |
| 2     | P0-T2       | Backend CI and staging   | Backend   | Laravel Dev | 2             | Backend only      |
| 3     | P0-T3       | API conventions and ADRs | Backend   | Laravel Dev | 1             | Backend only      |
| 6     | P0-T6       | Phase 0 integration gate | Both      | Both        | 1             | Both repositories |

**Task details**

<div class="joplin-table-wrapper"><table><thead><tr><th><p><strong>Task</strong></p></th><th><p><strong>Steps</strong></p></th><th><p><strong>Done when</strong></p></th></tr></thead><tbody><tr><td><p><strong>P0-T1</strong></p><p><strong>Laravel backend setup</strong></p><p>Backend · 2 d</p></td><td><ul><li>Create hms-backend with Laravel 11 and a MySQL 8 database; set DB_* in .env</li><li>Install Pest, Laravel Pint and Larastan; add composer scripts test, lint, analyse</li><li>Create app/Modules/* and app/Shared/* folders from the architecture section</li><li>Add .env.example (no real secrets) and docs/blueprint with the HMS documentation</li><li>Commit to Git with a README</li></ul></td><td><p>Tests, Pint and Larastan all run and pass on the empty project.</p></td></tr><tr><td><p><strong>P0-T2</strong></p><p><strong>Backend CI and staging</strong></p><p>Backend · 2 d</p></td><td><ul><li>Add a CI workflow: composer install, Pint check, Larastan, Pest on every pull request</li><li>Provision staging: Nginx, PHP-FPM, MySQL 8, Redis, Supervisor, HTTPS certificate</li><li>Create the staging database and .env on the server (secrets only there)</li><li>Deploy the empty API with a script and confirm it is served</li></ul></td><td><p>A pull request runs all checks and the empty API answers on the staging domain.</p></td></tr><tr><td><p><strong>P0-T3</strong></p><p><strong>API conventions and ADRs</strong></p><p>Backend · 1 d</p></td><td><ul><li>Create JSON success and error helpers (data, meta, error.code, message, fields, request_id)</li><li>Add request-ID middleware that sets X-Request-Id and logs it</li><li>Prefix routes with /api/v1 and add GET /api/v1/health</li><li>Create the OpenAPI skeleton with shared schemas and error codes</li><li>Write ADR-001 (modular monolith) and ADR-002 (two repositories, versioned API)</li></ul></td><td><p>Health endpoint returns the standard shape with a request id; both ADRs are committed.</p></td></tr><tr><td><p><strong>P0-T6</strong></p><p><strong>Phase 0 integration gate</strong></p><p>Both · 1 d</p></td><td><ul><li>Call /api/v1/health from the deployed React app and show the result on a temporary page</li><li>Confirm both repositories pass CI and deploy</li><li>Confirm blueprint and ADRs are committed in hms-backend/docs</li></ul></td><td><p>The frontend on staging reads the API health endpoint through the real domains.</p></td></tr></tbody></table></div>

<div class="joplin-table-wrapper"><table><tbody><tr><th><p><strong>Phase 0 gate — all must be true before Phase 1 starts</strong></p><ul><li>Both repositories pass CI and deploy to staging.</li><li>Frontend can call a health endpoint on the API through the real staging domains.</li><li>Blueprint and ADRs are committed in the backend docs folder.</li><li>All tasks in this file for the phase meet their "Done when" check and CI is green on this repository.</li><li>The phase is deployed to staging and the documentation and OpenAPI file are up to date.</li></ul></th></tr></tbody></table></div>

**Phase 1 — Foundation — Auth, Roles, Settings, Audit**

| **Goal**                 | Secure login, five roles with permissions, hospital settings and an audit trail that every later module relies on. |
| ------------------------ | ------------------------------------------------------------------------------------------------------------------ |
| **Covers**               | FR-AUTH, FR-USR, FR-SET, FR-AUD · Sections 8, 15, 16, 18                                                           |
| **Depends on**           | Phase 0                                                                                                            |
| **Effort here**          | 21 backend days + 4 shared days = 25 developer-days · 8 tasks                                                      |
| **Order (this folder)**  | T1 > T2 > T3 > T4 > T5 > T6 > T7 > T12                                                                             |
| **Order (both folders)** | B1 > B2 > F8 > B3 > B4 > F9 > B5 > B6 > F10 > B7 > F11 > X12 (B backend, F frontend, X shared)                     |

**Task list**

| **#** | **Task ID** | **Task**                                | **Layer** | **Who**     | **Est. Days** | **Pairs with**               |
| ----- | ----------- | --------------------------------------- | --------- | ----------- | ------------- | ---------------------------- |
| 7     | P1-T1       | Identity and RBAC database              | Backend   | Laravel Dev | 3             | Used by P1-T8                |
| 8     | P1-T2       | Sanctum authentication                  | Backend   | Laravel Dev | 3             | Used by P1-T8, P1-T9, P1-T11 |
| 9     | P1-T3       | Password reset and patient registration | Backend   | Laravel Dev | 3             | Used by P1-T8                |
| 10    | P1-T4       | Policies and permission middleware      | Backend   | Laravel Dev | 3             | Used by P1-T9, P1-T10        |
| 11    | P1-T5       | User administration                     | Backend   | Laravel Dev | 3             | Used by P1-T10               |
| 12    | P1-T6       | Settings, numbering and audit           | Backend   | Laravel Dev | 4             | Used by P1-T10               |
| 13    | P1-T7       | Error handling and API docs             | Backend   | Laravel Dev | 2             | Backend only                 |
| 18    | P1-T12      | Phase 1 QA                              | Both      | Both        | 4             | Both repositories            |

**Task details**

<div class="joplin-table-wrapper"><table><thead><tr><th><p><strong>Task</strong></p></th><th><p><strong>Steps</strong></p></th><th><p><strong>Done when</strong></p></th></tr></thead><tbody><tr><td><p><strong>P1-T1</strong></p><p><strong>Identity and RBAC database</strong></p><p>Backend · 3 d</p></td><td><ul><li>Migrations: users (is_active, failed_login_count, locked_until), roles, permissions, role_user, permission_role</li><li>Seeder: the five roles and every permission from the permission matrix</li><li>Seeder: role-permission links, first Admin (password from environment), default settings</li><li>Add model relationships and a helper that lists a user's permission names</li></ul></td><td><p>migrate:fresh --seed builds the database and the Admin loads with all permissions.</p></td></tr><tr><td><p><strong>P1-T2</strong></p><p><strong>Sanctum authentication</strong></p><p>Backend · 3 d</p></td><td><ul><li>Configure Sanctum SPA cookie mode: CORS with credentials, SESSION_DOMAIN, SANCTUM_STATEFUL_DOMAINS</li><li>Endpoints: /sanctum/csrf-cookie, POST /auth/login, POST /auth/logout, GET /auth/me (user, roles, permissions)</li><li>Rate-limit login; lock the account for 15 minutes after 5 failed attempts; neutral error text</li><li>Feature tests: success, wrong password, lockout, deactivated user</li></ul></td><td><p>Login, logout and /auth/me work with a cookie session; lockout triggers after five failures.</p></td></tr><tr><td><p><strong>P1-T3</strong></p><p><strong>Password reset and patient registration</strong></p><p>Backend · 3 d</p></td><td><ul><li>POST /auth/forgot-password and /auth/reset-password with neutral response, 60-minute single-use links</li><li>POST /auth/register for patient portal accounts and send the verification email</li><li>Block unverified accounts from the portal</li><li>Tests for link expiry, reuse and verification</li></ul></td><td><p>A user resets a password by email and a patient registers and verifies an account.</p></td></tr><tr><td><p><strong>P1-T4</strong></p><p><strong>Policies and permission middleware</strong></p><p>Backend · 3 d</p></td><td><ul><li>Create permission middleware (for example permission:patients.view) and register it</li><li>Create the base Policy with default deny and an ownership helper</li><li>Create a test helper that calls every route as each role and as a guest</li><li>Assert 401 for guests and 403 for roles without the permission</li></ul></td><td><p>A route with no permission check fails the route-coverage test; the role matrix tests pass (T-14).</p></td></tr><tr><td><p><strong>P1-T5</strong></p><p><strong>User administration</strong></p><p>Backend · 3 d</p></td><td><ul><li>Endpoints: users list, create, show, update; PATCH status; PUT roles</li><li>Validate unique email; send temporary password or invitation email</li><li>Block deactivating or demoting the last active Admin (BR-USR-02)</li><li>Tests for each rule and for permissions</li></ul></td><td><p>Admin manages users and the last Admin cannot be removed (T-12).</p></td></tr><tr><td><p><strong>P1-T6</strong></p><p><strong>Settings, numbering and audit</strong></p><p>Backend · 4 d</p></td><td><ul><li>settings table and service (get, set, typed defaults); GET/PUT /settings</li><li>sequences table and number service with a locked counter row</li><li>Auditable trait and observers writing audit_logs (old and new values, user, IP, user agent)</li><li>GET /audit-logs with filters; make the table append-only for the app database user</li><li>Tests including concurrent number generation</li></ul></td><td><p>Numbers stay unique under concurrent requests; user, login and settings changes appear in the audit log.</p></td></tr><tr><td><p><strong>P1-T7</strong></p><p><strong>Error handling and API docs</strong></p><p>Backend · 2 d</p></td><td><ul><li>Global exception handler mapping validation, auth, permission, not-found and domain exceptions to the standard error JSON</li><li>Create the domain exception base class carrying code and HTTP status</li><li>Create API Resources for user, role and permission</li><li>Document all Phase 1 endpoints in openapi.yaml</li></ul></td><td><p>Every error matches the standard shape and the OpenAPI file validates.</p></td></tr><tr><td><p><strong>P1-T12</strong></p><p><strong>Phase 1 QA</strong></p><p>Both · 4 d</p></td><td><ul><li>Verify cookie authentication across the two staging domains (CORS, cookie domain, CSRF)</li><li>Run the route-coverage and role-matrix tests</li><li>Check audit entries for login, failed login and user creation</li><li>Write ADR-003 (authentication decision) and fix issues</li></ul></td><td><p>All Phase 1 exit criteria pass and ADR-003 is committed.</p></td></tr></tbody></table></div>

<div class="joplin-table-wrapper"><table><tbody><tr><th><p><strong>Phase 1 gate — all must be true before Phase 2 starts</strong></p><ul><li>Each of the five roles logs in and sees only its own menu.</li><li>Every role gets 403 on endpoints it must not use (automated tests).</li><li>Login, failed login and user creation appear in the audit log.</li><li>Cookie authentication works across the React and API domains on staging.</li><li>All tasks in this file for the phase meet their "Done when" check and CI is green on this repository.</li><li>The phase is deployed to staging and the documentation and OpenAPI file are up to date.</li></ul></th></tr></tbody></table></div>

**Phase 2 — Patients**

| **Goal**                 | Register, find and manage patients with duplicate protection and an optional portal account. |
| ------------------------ | -------------------------------------------------------------------------------------------- |
| **Covers**               | FR-PAT-01 to 07 · BR-PAT-01 to 04                                                            |
| **Depends on**           | Phase 1                                                                                      |
| **Effort here**          | 7 backend days + 2 shared days = 9 developer-days · 4 tasks                                  |
| **Order (this folder)**  | T1 > T2 > T3 > T7                                                                            |
| **Order (both folders)** | B1 > B2 > F4 > B3 > F5 > F6 > X7 (B backend, F frontend, X shared)                           |

**Task list**

| **#** | **Task ID** | **Task**                          | **Layer** | **Who**     | **Est. Days** | **Pairs with**              |
| ----- | ----------- | --------------------------------- | --------- | ----------- | ------------- | --------------------------- |
| 19    | P2-T1       | Patient model and CRUD API        | Backend   | Laravel Dev | 3             | Used by P2-T4, P2-T5, P2-T6 |
| 20    | P2-T2       | Duplicate detection and lifecycle | Backend   | Laravel Dev | 2             | Used by P2-T4               |
| 21    | P2-T3       | Portal account linking            | Backend   | Laravel Dev | 2             | Used by P2-T5, P2-T6        |
| 25    | P2-T7       | Phase 2 QA                        | Both      | Both        | 2             | Both repositories           |

**Task details**

<div class="joplin-table-wrapper"><table><thead><tr><th><p><strong>Task</strong></p></th><th><p><strong>Steps</strong></p></th><th><p><strong>Done when</strong></p></th></tr></thead><tbody><tr><td><p><strong>P2-T1</strong></p><p><strong>Patient model and CRUD API</strong></p><p>Backend · 3 d</p></td><td><ul><li>patients migration with indexes on phone, name, date_of_birth, national_id</li><li>Patient number P-YYYY-NNNNNN from the sequence service</li><li>Endpoints: list with search (q), create, show, update, status</li><li>Policies: receptionist and Admin full; patient own; doctor assigned-only (stub)</li><li>Feature tests per role</li></ul></td><td><p>Patients are created and found by number, name, phone or national ID with correct role scoping.</p></td></tr><tr><td><p><strong>P2-T2</strong></p><p><strong>Duplicate detection and lifecycle</strong></p><p>Backend · 2 d</p></td><td><ul><li>Duplicate check on phone + date of birth, or full name + date of birth; return 409 DUPLICATE_PATIENT unless confirm_duplicate=true</li><li>Require guardian name and phone under 18</li><li>Deactivate and reactivate through status; never hard delete</li></ul></td><td><p>BR-PAT-01 to 04 tests pass.</p></td></tr><tr><td><p><strong>P2-T3</strong></p><p><strong>Portal account linking</strong></p><p>Backend · 2 d</p></td><td><ul><li>Link a portal user to a patient with patient number + date of birth, with attempt limits</li><li>Receptionist invitation: create the user and send the invitation email</li><li>GET /patients/{id}/timeline returning an empty structure for now</li></ul></td><td><p>A registered patient links to the correct record and cannot link to someone else's.</p></td></tr><tr><td><p><strong>P2-T7</strong></p><p><strong>Phase 2 QA</strong></p><p>Both · 2 d</p></td><td><ul><li>Test T-04: a patient cannot read another patient</li><li>Test duplicate scenarios and the under-18 guardian case</li><li>Fix issues and update OpenAPI</li></ul></td><td><p>Phase 2 exit criteria pass.</p></td></tr></tbody></table></div>

<div class="joplin-table-wrapper"><table><tbody><tr><th><p><strong>Phase 2 gate — all must be true before Phase 3 starts</strong></p><ul><li>FR-PAT-01 to 06 pass acceptance tests.</li><li>Duplicate warning works and BR-PAT tests pass.</li><li>A patient can see only their own data (T-04).</li><li>All tasks in this file for the phase meet their "Done when" check and CI is green on this repository.</li><li>The phase is deployed to staging and the documentation and OpenAPI file are up to date.</li></ul></th></tr></tbody></table></div>

**Phase 3 — Doctors and Schedules**

| **Goal**                 | Doctor profiles, departments, weekly availability, leave, and a reliable free-slot calculation. |
| ------------------------ | ----------------------------------------------------------------------------------------------- |
| **Covers**               | FR-DOC-01 to 06 · BR-APT-02                                                                     |
| **Depends on**           | Phase 1 (can run in parallel with Phase 2)                                                      |
| **Effort here**          | 9 backend days + 2 shared days = 11 developer-days · 5 tasks                                    |
| **Order (this folder)**  | T1 > T2 > T3 > T4 > T8                                                                          |
| **Order (both folders)** | B1 > B2 > F6 > B3 > F7 > B4 > F5 > X8 (B backend, F frontend, X shared)                         |

**Task list**

| **#** | **Task ID** | **Task**                        | **Layer** | **Who**     | **Est. Days** | **Pairs with**       |
| ----- | ----------- | ------------------------------- | --------- | ----------- | ------------- | -------------------- |
| 26    | P3-T1       | Departments and specializations | Backend   | Laravel Dev | 1             | Used by P3-T6        |
| 27    | P3-T2       | Doctor account and profile      | Backend   | Laravel Dev | 3             | Used by P3-T5, P3-T6 |
| 28    | P3-T3       | Schedules and leave API         | Backend   | Laravel Dev | 2             | Used by P3-T7        |
| 29    | P3-T4       | Available-slot service          | Backend   | Laravel Dev | 3             | Used by P3-T5        |
| 33    | P3-T8       | Phase 3 QA                      | Both      | Both        | 2             | Both repositories    |

**Task details**

<div class="joplin-table-wrapper"><table><thead><tr><th><p><strong>Task</strong></p></th><th><p><strong>Steps</strong></p></th><th><p><strong>Done when</strong></p></th></tr></thead><tbody><tr><td><p><strong>P3-T1</strong></p><p><strong>Departments and specializations</strong></p><p>Backend · 1 d</p></td><td><ul><li>Migrations and CRUD endpoints for departments and specializations</li><li>Admin manages; all roles can read</li><li>Seed a starter list</li></ul></td><td><p>Admin maintains master data; other roles read only.</p></td></tr><tr><td><p><strong>P3-T2</strong></p><p><strong>Doctor account and profile</strong></p><p>Backend · 3 d</p></td><td><ul><li>doctors migration: user_id, specialization, department, licence, fee, slot length</li><li>Create doctor: create user, assign doctor role and create profile in one transaction</li><li>List with filters (specialization, name, active), update, deactivate with future-appointment check (stub until Phase 5)</li><li>Tests: only Admin creates; receptionist views</li></ul></td><td><p>A doctor can be created, listed, filtered and deactivated safely.</p></td></tr><tr><td><p><strong>P3-T3</strong></p><p><strong>Schedules and leave API</strong></p><p>Backend · 2 d</p></td><td><ul><li>doctor_schedules and doctor_leaves migrations with unique and range indexes</li><li>GET and PUT /doctors/{id}/schedules (replace weekly schedule); end time must be after start</li><li>Leaves: list, create, delete; return affected appointments (stub)</li><li>Doctor edits only own schedule; Admin any</li></ul></td><td><p>Schedules and leave are saved and validated with correct permissions.</p></td></tr><tr><td><p><strong>P3-T4</strong></p><p><strong>Available-slot service</strong></p><p>Backend · 3 d</p></td><td><ul><li>Build slots from the weekly schedule and slot length</li><li>Remove leave dates, past times and already booked slots</li><li>Expose GET /doctors/{id}/available-slots?date=</li><li>Unit tests: boundaries, leave day, today in the past, different slot lengths</li></ul></td><td><p>Slot generator tests pass for all edge cases (BR-APT-02).</p></td></tr><tr><td><p><strong>P3-T8</strong></p><p><strong>Phase 3 QA</strong></p><p>Both · 2 d</p></td><td><ul><li>Test leave, past times and slot boundaries in the API and the UI</li><li>Verify only Admin creates doctors and Receptionist can view them</li><li>Fix issues and update OpenAPI</li></ul></td><td><p>Phase 3 exit criteria pass.</p></td></tr></tbody></table></div>

<div class="joplin-table-wrapper"><table><tbody><tr><th><p><strong>Phase 3 gate — all must be true before Phase 4 starts</strong></p><ul><li>Available slots are correct for schedules, leave days and slot boundaries.</li><li>Slot generator unit tests pass.</li><li>Only Admin can create doctors; receptionist can view them.</li><li>All tasks in this file for the phase meet their "Done when" check and CI is green on this repository.</li><li>The phase is deployed to staging and the documentation and OpenAPI file are up to date.</li></ul></th></tr></tbody></table></div>

**Phase 4 — Billing, Payments and Expenses**

| **Goal**                 | Price list, invoices, payments, refunds with approval, a payment gateway interface and expense tracking — built before appointments because booking creates an invoice. |
| ------------------------ | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Covers**               | FR-BIL-01 to 09 · BR-BIL-01 to 10                                                                                                                                       |
| **Depends on**           | Phase 2                                                                                                                                                                 |
| **Effort here**          | 20 backend days + 3 shared days = 23 developer-days · 8 tasks                                                                                                           |
| **Order (this folder)**  | T1 > T2 > T3 > T4 > T5 > T6 > T7 > T12                                                                                                                                  |
| **Order (both folders)** | B1 > F8 > B2 > F9 > B3 > B4 > B5 > F10 > B6 > B7 > F11 > X12 (B backend, F frontend, X shared)                                                                          |

**Task list**

| **#** | **Task ID** | **Task**                   | **Layer** | **Who**     | **Est. Days** | **Pairs with**         |
| ----- | ----------- | -------------------------- | --------- | ----------- | ------------- | ---------------------- |
| 34    | P4-T1       | Service price list         | Backend   | Laravel Dev | 1             | Used by P4-T8          |
| 35    | P4-T2       | Invoices and items         | Backend   | Laravel Dev | 5             | Used by P4-T9, P4-T11  |
| 36    | P4-T3       | Payment recording          | Backend   | Laravel Dev | 3             | Used by P4-T10, P4-T11 |
| 37    | P4-T4       | Refunds and discounts      | Backend   | Laravel Dev | 3             | Used by P4-T10         |
| 38    | P4-T5       | Online payment integration | Backend   | Laravel Dev | 4             | Used by P4-T10         |
| 39    | P4-T6       | Expense management         | Backend   | Laravel Dev | 2             | Used by P4-T11         |
| 40    | P4-T7       | Invoice and receipt PDFs   | Backend   | Laravel Dev | 2             | Backend only           |
| 45    | P4-T12      | Phase 4 financial QA       | Both      | Both        | 3             | Both repositories      |

**Task details**

<div class="joplin-table-wrapper"><table><thead><tr><th><p><strong>Task</strong></p></th><th><p><strong>Steps</strong></p></th><th><p><strong>Done when</strong></p></th></tr></thead><tbody><tr><td><p><strong>P4-T1</strong></p><p><strong>Service price list</strong></p><p>Backend · 1 d</p></td><td><ul><li>services migration and CRUD: code, name, category, price, tax rate, active</li><li>Admin and Accountant manage; other roles read</li><li>Seed consultation and bed-charge services</li></ul></td><td><p>The price list is maintained by billing roles and readable by others.</p></td></tr><tr><td><p><strong>P4-T2</strong></p><p><strong>Invoices and items</strong></p><p>Backend · 5 d</p></td><td><ul><li>invoices and invoice_items migrations with checks (paid amount between 0 and total)</li><li>Calculator: sum of quantity x price, minus discount, plus tax, rounded to 2 decimals</li><li>Number INV-YYYY-NNNNNN from the sequence service; draft, issue, cancel transitions</li><li>Lock issued invoices; only drafts are editable (BR-BIL-03)</li><li>Policy and feature tests per role</li></ul></td><td><p>BR-BIL-01 to 03 tests pass and totals match worked examples (T-06).</p></td></tr><tr><td><p><strong>P4-T3</strong></p><p><strong>Payment recording</strong></p><p>Backend · 3 d</p></td><td><ul><li>payments migration: type, amount, method, reference, status</li><li>POST /invoices/{id}/payments with Idempotency-Key; cash, card, bank, cheque</li><li>Update paid amount and status (issued, partially_paid, paid); block overpayment</li></ul></td><td><p>Partial and full payments set the right status; duplicates are ignored (T-07).</p></td></tr><tr><td><p><strong>P4-T4</strong></p><p><strong>Refunds and discounts</strong></p><p>Backend · 3 d</p></td><td><ul><li>Refund as a separate payment row of type refund linked to the original; never above the amount paid</li><li>Approval rules: discount or refund over the limit needs Admin; requester cannot approve own request</li><li>Endpoints: refund and approve-refund; Admin notification event stub</li></ul></td><td><p>T-13 passes: a large refund waits for Admin and the requester cannot approve it.</p></td></tr><tr><td><p><strong>P4-T5</strong></p><p><strong>Online payment integration</strong></p><p>Backend · 4 d</p></td><td><ul><li>Payment provider interface with a sandbox implementation for the chosen gateway</li><li>POST /payments/online/initiate returning checkout data</li><li>Webhook endpoint: verify signature, apply once using the unique (gateway, reference)</li><li>Tests with valid, invalid and repeated webhooks</li></ul></td><td><p>T-08 passes: bad signature rejected, valid webhook applied once.</p></td></tr><tr><td><p><strong>P4-T6</strong></p><p><strong>Expense management</strong></p><p>Backend · 2 d</p></td><td><ul><li>expense_categories and expenses migrations; expense number</li><li>Create, update, list, void with reason (never delete); attachments on private storage</li><li>Accountant and Admin permissions</li></ul></td><td><p>Expenses are recorded and voided with an audit trail.</p></td></tr><tr><td><p><strong>P4-T7</strong></p><p><strong>Invoice and receipt PDFs</strong></p><p>Backend · 2 d</p></td><td><ul><li>PDF templates for invoice and receipt with hospital name and logo from settings</li><li>GET /invoices/{id}/pdf with permission and ownership checks</li></ul></td><td><p>A correct PDF downloads for an invoice and for a payment receipt.</p></td></tr><tr><td><p><strong>P4-T12</strong></p><p><strong>Phase 4 financial QA</strong></p><p>Both · 3 d</p></td><td><ul><li>Run T-06, T-07, T-08 and T-13</li><li>Reconcile a sample: invoice totals, payments, refunds and the dashboard</li><li>Fix issues and update OpenAPI</li></ul></td><td><p>Phase 4 exit criteria pass.</p></td></tr></tbody></table></div>

<div class="joplin-table-wrapper"><table><tbody><tr><th><p><strong>Phase 4 gate — all must be true before Phase 5 starts</strong></p><ul><li>Issued invoice cannot be edited (T-06); overpayment and duplicate payment are blocked (T-07).</li><li>Webhook with bad signature is rejected and a valid one is applied once (T-08).</li><li>Large refund waits for Admin and requester cannot approve (T-13).</li><li>Accountant dashboard numbers match the data.</li><li>All tasks in this file for the phase meet their "Done when" check and CI is green on this repository.</li><li>The phase is deployed to staging and the documentation and OpenAPI file are up to date.</li></ul></th></tr></tbody></table></div>

**Phase 5 — Appointments**

| **Goal**                 | Slot-safe booking with fee payment, cancellation and refunds, check-in with token numbers, for patients and receptionists. |
| ------------------------ | -------------------------------------------------------------------------------------------------------------------------- |
| **Covers**               | FR-APT-01 to 10 · BR-APT-01 to 10                                                                                          |
| **Depends on**           | Phase 3 and Phase 4                                                                                                        |
| **Effort here**          | 15 backend days + 4 shared days = 19 developer-days · 6 tasks                                                              |
| **Order (this folder)**  | T1 > T4 > T2 > T3 > T5 > T10                                                                                               |
| **Order (both folders)** | B1 > B4 > B2 > B3 > B5 > F6 > F7 > F8 > F9 > X10 (B backend, F frontend, X shared)                                         |

**Task list**

| **#** | **Task ID** | **Task**                               | **Layer** | **Who**     | **Est. Days** | **Pairs with**                     |
| ----- | ----------- | -------------------------------------- | --------- | ----------- | ------------- | ---------------------------------- |
| 46    | P5-T1       | Appointment schema and booking service | Backend   | Laravel Dev | 5             | Used by P5-T6, P5-T7               |
| 47    | P5-T2       | Cancellation, rescheduling and refunds | Backend   | Laravel Dev | 4             | Used by P5-T8                      |
| 48    | P5-T3       | Check-in and tokens                    | Backend   | Laravel Dev | 2             | Used by P5-T9                      |
| 49    | P5-T4       | Scheduled jobs                         | Backend   | Laravel Dev | 2             | Backend only                       |
| 50    | P5-T5       | Role-scoped appointment APIs           | Backend   | Laravel Dev | 2             | Used by P5-T6, P5-T7, P5-T8, P5-T9 |
| 55    | P5-T10      | Phase 5 QA                             | Both      | Both        | 4             | Both repositories                  |

**Task details**

<div class="joplin-table-wrapper"><table><thead><tr><th><p><strong>Task</strong></p></th><th><p><strong>Steps</strong></p></th><th><p><strong>Done when</strong></p></th></tr></thead><tbody><tr><td><p><strong>P5-T1</strong></p><p><strong>Appointment schema and booking service</strong></p><p>Backend · 5 d</p></td><td><ul><li>appointments migration with unique (doctor_id, appointment_date, start_time, is_active)</li><li>Booking in one transaction: validate the slot, check BR-APT-04, create pending_payment with hold_expires_at, create the consultation-fee invoice with a fee snapshot</li><li>Map the unique-index violation to 409 SLOT_TAKEN</li><li>Dispatch AppointmentBooked after commit</li><li>Tests including two simultaneous bookings</li></ul></td><td><p>T-01 passes: two users booking the same slot, one succeeds and one gets 409.</p></td></tr><tr><td><p><strong>P5-T2</strong></p><p><strong>Cancellation, rescheduling and refunds</strong></p><p>Backend · 4 d</p></td><td><ul><li>Refund calculator: 100% at 24 h or more, 50% at 2-24 h, 0% under 2 h; hospital cancellation 100%; values from settings</li><li>Cancel: status, reason, free the slot, create the refund through billing</li><li>Reschedule: new appointment linked to the old one, payment moved (BR-APT-10)</li><li>Patient cut-off rule; receptionist and Admin override with a reason</li></ul></td><td><p>T-03 passes: refunds follow the tiers at 30 h, 10 h and 1 h.</p></td></tr><tr><td><p><strong>P5-T3</strong></p><p><strong>Check-in and tokens</strong></p><p>Backend · 2 d</p></td><td><ul><li>POST /appointments/{id}/check-in: same day only, from scheduled, assigns the next token for that doctor and day</li><li>POST /appointments/{id}/no-show as a manual action</li></ul></td><td><p>Check-in assigns unique daily tokens and invalid states return 409.</p></td></tr><tr><td><p><strong>P5-T4</strong></p><p><strong>Scheduled jobs</strong></p><p>Backend · 2 d</p></td><td><ul><li>ExpirePendingAppointments every minute: pending_payment past its hold becomes expired and frees the slot</li><li>MarkNoShows every 15 minutes (30 minutes after end time)</li><li>Register both in the scheduler with tests</li></ul></td><td><p>T-02 passes: an unpaid hold expires and the slot is free again.</p></td></tr><tr><td><p><strong>P5-T5</strong></p><p><strong>Role-scoped appointment APIs</strong></p><p>Backend · 2 d</p></td><td><ul><li>GET /appointments scoped by role: patient own, doctor own, reception and Admin all</li><li>Filters: date, doctor, patient, status; sorting and pagination</li><li>Include payment status and token in the resource</li></ul></td><td><p>Each role sees only its own scope in tests.</p></td></tr><tr><td><p><strong>P5-T10</strong></p><p><strong>Phase 5 QA</strong></p><p>Both · 4 d</p></td><td><ul><li>Run T-01, T-02 and T-03</li><li>Browser journeys: patient books and pays; walk-in with cash</li><li>Fix issues and update OpenAPI</li></ul></td><td><p>Phase 5 exit criteria pass.</p></td></tr></tbody></table></div>

<div class="joplin-table-wrapper"><table><tbody><tr><th><p><strong>Phase 5 gate — all must be true before Phase 6 starts</strong></p><ul><li>Two users booking the same slot: one succeeds, one gets 409 (T-01).</li><li>Unpaid holds expire and free the slot (T-02); refunds follow the tiers (T-03).</li><li>End-to-end journeys 1 (patient books) and 2 (walk-in) pass in a browser.</li><li>All tasks in this file for the phase meet their "Done when" check and CI is green on this repository.</li><li>The phase is deployed to staging and the documentation and OpenAPI file are up to date.</li></ul></th></tr></tbody></table></div>

**Phase 6 — Consultations, Prescriptions and Medical Records**

| **Goal**                 | The doctor workspace: vitals, diagnoses, prescriptions and the patient record, with strict privacy rules. |
| ------------------------ | --------------------------------------------------------------------------------------------------------- |
| **Covers**               | FR-CON-01 to 09 · BR-CON-01 to 07 · AR-02, AR-03                                                          |
| **Depends on**           | Phase 5                                                                                                   |
| **Effort here**          | 18 backend days + 4 shared days = 22 developer-days · 6 tasks                                             |
| **Order (this folder)**  | T1 > T4 > T3 > T2 > T5 > T10                                                                              |
| **Order (both folders)** | B1 > B4 > B3 > B2 > B5 > F7 > F6 > F8 > F9 > X10 (B backend, F frontend, X shared)                        |

**Task list**

| **#** | **Task ID** | **Task**                    | **Layer** | **Who**     | **Est. Days** | **Pairs with**       |
| ----- | ----------- | --------------------------- | --------- | ----------- | ------------- | -------------------- |
| 56    | P6-T1       | Consultation lifecycle      | Backend   | Laravel Dev | 5             | Used by P6-T6        |
| 57    | P6-T2       | Medicines and prescriptions | Backend   | Laravel Dev | 4             | Used by P6-T6, P6-T8 |
| 58    | P6-T3       | Medical records             | Backend   | Laravel Dev | 3             | Used by P6-T6, P6-T7 |
| 59    | P6-T4       | Clinical access and audit   | Backend   | Laravel Dev | 4             | Used by P6-T7        |
| 60    | P6-T5       | Patient timeline API        | Backend   | Laravel Dev | 2             | Used by P6-T9        |
| 65    | P6-T10      | Phase 6 QA                  | Both      | Both        | 4             | Both repositories    |

**Task details**

<div class="joplin-table-wrapper"><table><thead><tr><th><p><strong>Task</strong></p></th><th><p><strong>Steps</strong></p></th><th><p><strong>Done when</strong></p></th></tr></thead><tbody><tr><td><p><strong>P6-T1</strong></p><p><strong>Consultation lifecycle</strong></p><p>Backend · 5 d</p></td><td><ul><li>Migrations: consultations, vitals, diagnoses, consultation_diagnoses, consultation_addenda</li><li>Start from a checked_in appointment (BR-APT-09); appointment becomes in_consultation</li><li>Update symptoms, notes, plan, follow-up, vitals and diagnoses while in progress</li><li>Complete: lock it and set the appointment to completed; later changes only as addenda</li><li>Policy: only the assigned doctor (BR-CON-02)</li></ul></td><td><p>BR-CON-01 to 04 tests pass; T-09 passes.</p></td></tr><tr><td><p><strong>P6-T2</strong></p><p><strong>Medicines and prescriptions</strong></p><p>Backend · 4 d</p></td><td><ul><li>medicines catalogue CRUD (Admin) and a search endpoint</li><li>prescriptions and prescription_items with numbers; rules BR-CON-05 and 06</li><li>Allergy check against the patient's allergy entries (BR-CON-07)</li><li>GET /prescriptions/{id}/pdf; cancel with reason</li></ul></td><td><p>A doctor issues, cancels and prints prescriptions within the rules.</p></td></tr><tr><td><p><strong>P6-T3</strong></p><p><strong>Medical records</strong></p><p>Backend · 3 d</p></td><td><ul><li>medical_records: allergy, chronic condition, surgery, family history, immunisation, note</li><li>attachments on a private disk: type and size limits, random names, authorised URLs</li><li>Create and update by the assigned doctor; read by the owner patient</li></ul></td><td><p>Records and attachments are saved and served only to permitted users.</p></td></tr><tr><td><p><strong>P6-T4</strong></p><p><strong>Clinical access and audit</strong></p><p>Backend · 4 d</p></td><td><ul><li>Care-relationship policy: a doctor reaches only patients with an appointment, consultation or admission (AR-02)</li><li>Break-glass: X-Access-Reason header, logged and flagged (AR-03)</li><li>Audit viewed events for patient record, consultation and records</li><li>Field-level hiding for receptionist and accountant (AR-06)</li></ul></td><td><p>T-04 and T-05 pass.</p></td></tr><tr><td><p><strong>P6-T5</strong></p><p><strong>Patient timeline API</strong></p><p>Backend · 2 d</p></td><td><ul><li>GET /patients/{id}/timeline merging consultations, diagnoses, prescriptions and records, newest first</li><li>Pagination and role scoping</li></ul></td><td><p>The timeline returns correct, scoped history.</p></td></tr><tr><td><p><strong>P6-T10</strong></p><p><strong>Phase 6 QA</strong></p><p>Both · 4 d</p></td><td><ul><li>Run T-04, T-05 and T-09</li><li>Browser journey 3: check-in to prescription</li><li>Fix issues and update OpenAPI</li></ul></td><td><p>Phase 6 exit criteria pass.</p></td></tr></tbody></table></div>

<div class="joplin-table-wrapper"><table><tbody><tr><th><p><strong>Phase 6 gate — all must be true before Phase 7 starts</strong></p><ul><li>Doctor cannot open a patient without a care relationship unless break-glass reason is given, and it is logged (T-05).</li><li>Completed consultation cannot be edited, only addenda (T-09).</li><li>End-to-end journey 3 (consultation to prescription) passes.</li><li>All tasks in this file for the phase meet their "Done when" check and CI is green on this repository.</li><li>The phase is deployed to staging and the documentation and OpenAPI file are up to date.</li></ul></th></tr></tbody></table></div>

**Phase 7 — Admissions, Wards and Beds**

| **Goal**                 | Admit, transfer and discharge patients with correct bed occupancy and automatic bed charges. |
| ------------------------ | -------------------------------------------------------------------------------------------- |
| **Covers**               | FR-ADM-01 to 07 · BR-ADM-01 to 06                                                            |
| **Depends on**           | Phase 4 (can run in parallel with Phase 6)                                                   |
| **Effort here**          | 10 backend days + 3 shared days = 13 developer-days · 4 tasks                                |
| **Order (this folder)**  | T1 > T2 > T3 > T7                                                                            |
| **Order (both folders)** | B1 > F4 > B2 > F5 > B3 > F6 > X7 (B backend, F frontend, X shared)                           |

**Task list**

| **#** | **Task ID** | **Task**                     | **Layer** | **Who**     | **Est. Days** | **Pairs with**       |
| ----- | ----------- | ---------------------------- | --------- | ----------- | ------------- | -------------------- |
| 66    | P7-T1       | Wards, beds and availability | Backend   | Laravel Dev | 2             | Used by P7-T4, P7-T5 |
| 67    | P7-T2       | Admissions and transfers     | Backend   | Laravel Dev | 4             | Used by P7-T5, P7-T6 |
| 68    | P7-T3       | Discharge and bed charges    | Backend   | Laravel Dev | 4             | Used by P7-T6        |
| 72    | P7-T7       | Phase 7 QA                   | Both      | Both        | 3             | Both repositories    |

**Task details**

<div class="joplin-table-wrapper"><table><thead><tr><th><p><strong>Task</strong></p></th><th><p><strong>Steps</strong></p></th><th><p><strong>Done when</strong></p></th></tr></thead><tbody><tr><td><p><strong>P7-T1</strong></p><p><strong>Wards, beds and availability</strong></p><p>Backend · 2 d</p></td><td><ul><li>wards (type, daily rate) and beds (status) migrations and CRUD for Admin</li><li>GET /beds/availability by ward</li><li>Seed sample wards</li></ul></td><td><p>Admin maintains wards and beds; availability is correct.</p></td></tr><tr><td><p><strong>P7-T2</strong></p><p><strong>Admissions and transfers</strong></p><p>Backend · 4 d</p></td><td><ul><li>admissions migration with active_bed_key and active_patient_key unique indexes</li><li>Admit: number ADM-YYYY-NNNNNN, bed becomes occupied; errors BED_OCCUPIED and PATIENT_ALREADY_ADMITTED</li><li>Transfer: bed_transfers record, free the old bed, occupy the new one</li><li>Policies: receptionist and Admin create; assigned doctor updates</li></ul></td><td><p>T-10 passes.</p></td></tr><tr><td><p><strong>P7-T3</strong></p><p><strong>Discharge and bed charges</strong></p><p>Backend · 4 d</p></td><td><ul><li>Doctor discharge summary: diagnosis, condition, instructions</li><li>Discharge time not before admission; bed freed</li><li>Bed charges = days (rounded up, minimum 1) x ward rate, added to the admission invoice</li><li>Clearance rule: block with an outstanding balance unless Admin overrides with a reason</li></ul></td><td><p>T-11 passes.</p></td></tr><tr><td><p><strong>P7-T7</strong></p><p><strong>Phase 7 QA</strong></p><p>Both · 3 d</p></td><td><ul><li>Run T-10 and T-11</li><li>Browser journey 5</li><li>Fix issues and update OpenAPI</li></ul></td><td><p>Phase 7 exit criteria pass.</p></td></tr></tbody></table></div>

<div class="joplin-table-wrapper"><table><tbody><tr><th><p><strong>Phase 7 gate — all must be true before Phase 8 starts</strong></p><ul><li>One bed cannot hold two patients and one patient cannot be admitted twice (T-10).</li><li>Discharge adds correct bed charges and frees the bed (T-11).</li><li>End-to-end journey 5 passes.</li><li>All tasks in this file for the phase meet their "Done when" check and CI is green on this repository.</li><li>The phase is deployed to staging and the documentation and OpenAPI file are up to date.</li></ul></th></tr></tbody></table></div>

**Phase 8 — Notifications**

| **Goal**                 | Reliable email (and optional SMS) plus in-app notifications driven by domain events. |
| ------------------------ | ------------------------------------------------------------------------------------ |
| **Covers**               | FR-NOT-01, FR-NOT-02 · Section 17                                                    |
| **Depends on**           | Phase 5                                                                              |
| **Effort here**          | 8 backend days + 2 shared days = 10 developer-days · 4 tasks                         |
| **Order (this folder)**  | T1 > T2 > T3 > T5                                                                    |
| **Order (both folders)** | B1 > B2 > B3 > F4 > X5 (B backend, F frontend, X shared)                             |

**Task list**

| **#** | **Task ID** | **Task**                              | **Layer** | **Who**     | **Est. Days** | **Pairs with**    |
| ----- | ----------- | ------------------------------------- | --------- | ----------- | ------------- | ----------------- |
| 73    | P8-T1       | Event-driven email                    | Backend   | Laravel Dev | 4             | Backend only      |
| 74    | P8-T2       | Appointment reminders and SMS adapter | Backend   | Laravel Dev | 2             | Backend only      |
| 75    | P8-T3       | In-app notifications API              | Backend   | Laravel Dev | 2             | Used by P8-T4     |
| 77    | P8-T5       | Phase 8 QA                            | Both      | Both        | 2             | Both repositories |

**Task details**

<div class="joplin-table-wrapper"><table><thead><tr><th><p><strong>Task</strong></p></th><th><p><strong>Steps</strong></p></th><th><p><strong>Done when</strong></p></th></tr></thead><tbody><tr><td><p><strong>P8-T1</strong></p><p><strong>Event-driven email</strong></p><p>Backend · 4 d</p></td><td><ul><li>Domain events: booked, paid, rescheduled, cancelled, refund, account, password reset, dispatched after commit</li><li>Listeners create queued notifications; email templates without medical detail</li><li>Retry 3 times with backoff; failed jobs visible to Admin</li></ul></td><td><p>Each listed event sends exactly one email in tests.</p></td></tr><tr><td><p><strong>P8-T2</strong></p><p><strong>Appointment reminders and SMS adapter</strong></p><p>Backend · 2 d</p></td><td><ul><li>Hourly job for 24-hour reminders</li><li>SMS adapter interface with a null implementation until a provider is chosen (OQ-08)</li></ul></td><td><p>Reminders are sent once per appointment.</p></td></tr><tr><td><p><strong>P8-T3</strong></p><p><strong>In-app notifications API</strong></p><p>Backend · 2 d</p></td><td><ul><li>Use the notifications table; list, read and read-all endpoints</li><li>Expose the unread count</li></ul></td><td><p>Users read only their own notifications.</p></td></tr><tr><td><p><strong>P8-T5</strong></p><p><strong>Phase 8 QA</strong></p><p>Both · 2 d</p></td><td><ul><li>Check retries and failed-job display</li><li>Confirm no diagnosis or medicine appears in message bodies</li><li>Fix issues</li></ul></td><td><p>Phase 8 exit criteria pass.</p></td></tr></tbody></table></div>

<div class="joplin-table-wrapper"><table><tbody><tr><th><p><strong>Phase 8 gate — all must be true before Phase 9 starts</strong></p><ul><li>Every event in Section 17 fires exactly once after commit.</li><li>Failed sends are retried 3 times and visible to Admin.</li><li>No diagnosis or medicine names appear in emails.</li><li>All tasks in this file for the phase meet their "Done when" check and CI is green on this repository.</li><li>The phase is deployed to staging and the documentation and OpenAPI file are up to date.</li></ul></th></tr></tbody></table></div>

**Phase 9 — Reports and Dashboards**

| **Goal**                 | Role dashboards and the operational, clinical and financial reports with CSV export. |
| ------------------------ | ------------------------------------------------------------------------------------ |
| **Covers**               | FR-REP-01 · Section 21                                                               |
| **Depends on**           | Phases 4 to 7                                                                        |
| **Effort here**          | 10 backend days + 2 shared days = 12 developer-days · 4 tasks                        |
| **Order (this folder)**  | T1 > T2 > T3 > T6                                                                    |
| **Order (both folders)** | B1 > F4 > B2 > B3 > F5 > X6 (B backend, F frontend, X shared)                        |

**Task list**

| **#** | **Task ID** | **Task**             | **Layer** | **Who**     | **Est. Days** | **Pairs with**    |
| ----- | ----------- | -------------------- | --------- | ----------- | ------------- | ----------------- |
| 78    | P9-T1       | Role dashboard APIs  | Backend   | Laravel Dev | 3             | Used by P9-T4     |
| 79    | P9-T2       | Report query classes | Backend   | Laravel Dev | 5             | Used by P9-T5     |
| 80    | P9-T3       | Queued CSV exports   | Backend   | Laravel Dev | 2             | Used by P9-T5     |
| 83    | P9-T6       | Phase 9 QA           | Both      | Both        | 2             | Both repositories |

**Task details**

<div class="joplin-table-wrapper"><table><thead><tr><th><p><strong>Task</strong></p></th><th><p><strong>Steps</strong></p></th><th><p><strong>Done when</strong></p></th></tr></thead><tbody><tr><td><p><strong>P9-T1</strong></p><p><strong>Role dashboard APIs</strong></p><p>Backend · 3 d</p></td><td><ul><li>GET /dashboard returning widgets by role</li><li>Check query efficiency and indexes</li></ul></td><td><p>Each role gets only its own widgets.</p></td></tr><tr><td><p><strong>P9-T2</strong></p><p><strong>Report query classes</strong></p><p>Backend · 5 d</p></td><td><ul><li>One query class per report: operational, clinical, financial</li><li>Filters, pagination and reports.* permissions</li><li>Count only succeeded payments; exclude cancelled invoices and voided expenses</li></ul></td><td><p>Report totals reconcile with source records.</p></td></tr><tr><td><p><strong>P9-T3</strong></p><p><strong>Queued CSV exports</strong></p><p>Backend · 2 d</p></td><td><ul><li>format=csv through a queued job; notify when ready</li><li>Private download link; log exports in the audit log</li></ul></td><td><p>Large exports finish without timeouts.</p></td></tr><tr><td><p><strong>P9-T6</strong></p><p><strong>Phase 9 QA</strong></p><p>Both · 2 d</p></td><td><ul><li>Reconcile figures with source records</li><li>Performance check with realistic data volume</li><li>Fix issues</li></ul></td><td><p>Phase 9 exit criteria pass.</p></td></tr></tbody></table></div>

<div class="joplin-table-wrapper"><table><tbody><tr><th><p><strong>Phase 9 gate — all must be true before Phase 10 starts</strong></p><ul><li>Report totals reconcile with invoices, payments and appointments.</li><li>Heavy reports meet the performance target with realistic data.</li><li>Each role sees only the reports its permissions allow.</li><li>All tasks in this file for the phase meet their "Done when" check and CI is green on this repository.</li><li>The phase is deployed to staging and the documentation and OpenAPI file are up to date.</li></ul></th></tr></tbody></table></div>

**Phase 10 — Lab Module (Optional)**

| **Goal**                 | Doctors order lab tests, results are recorded, and tests flow into billing. |
| ------------------------ | --------------------------------------------------------------------------- |
| **Covers**               | FR-CON-10 · lab tables in Section 12                                        |
| **Depends on**           | Phases 4 and 6                                                              |
| **Effort here**          | 5 backend days + 2 shared days = 7 developer-days · 2 tasks                 |
| **Order (this folder)**  | T1 > T3                                                                     |
| **Order (both folders)** | B1 > F2 > X3 (B backend, F frontend, X shared)                              |

**Task list**

| **#** | **Task ID** | **Task**                          | **Layer** | **Who**     | **Est. Days** | **Pairs with**    |
| ----- | ----------- | --------------------------------- | --------- | ----------- | ------------- | ----------------- |
| 84    | P10-T1      | Lab catalogue, orders and results | Backend   | Laravel Dev | 5             | Used by P10-T2    |
| 86    | P10-T3      | Phase 10 QA                       | Both      | Both        | 2             | Both repositories |

**Task details**

<div class="joplin-table-wrapper"><table><thead><tr><th><p><strong>Task</strong></p></th><th><p><strong>Steps</strong></p></th><th><p><strong>Done when</strong></p></th></tr></thead><tbody><tr><td><p><strong>P10-T1</strong></p><p><strong>Lab catalogue, orders and results</strong></p><p>Backend · 5 d</p></td><td><ul><li>lab_tests, lab_orders and lab_order_items migrations and CRUD</li><li>Doctor orders from a consultation; results entered; status changes</li><li>Add lab tests to invoices; notifications</li></ul></td><td><p>A lab order appears on the invoice and results are visible to the right roles.</p></td></tr><tr><td><p><strong>P10-T3</strong></p><p><strong>Phase 10 QA</strong></p><p>Both · 2 d</p></td><td><ul><li>Test permissions and the invoice link</li><li>Fix issues</li></ul></td><td><p>Phase 10 exit criteria pass.</p></td></tr></tbody></table></div>

<div class="joplin-table-wrapper"><table><tbody><tr><th><p><strong>Phase 10 gate — all must be true before Phase 11 starts</strong></p><ul><li>Lab orders appear on invoices.</li><li>Only permitted roles can order or enter results.</li><li>Patients see only their own published results.</li><li>All tasks in this file for the phase meet their "Done when" check and CI is green on this repository.</li><li>The phase is deployed to staging and the documentation and OpenAPI file are up to date.</li></ul></th></tr></tbody></table></div>

**Phase 11 — Hardening, UAT and Release**

| **Goal**                 | Security, performance, backups, user acceptance testing and the production launch. |
| ------------------------ | ---------------------------------------------------------------------------------- |
| **Covers**               | Sections 16, 22, 24                                                                |
| **Depends on**           | All earlier phases                                                                 |
| **Effort here**          | 10 backend days + 9 shared days = 19 developer-days · 6 tasks                      |
| **Order (this folder)**  | T1 > T2 > T3 > T5 > T6 > T7                                                        |
| **Order (both folders)** | B1 > B2 > B3 > F4 > X5 > X6 > X7 (B backend, F frontend, X shared)                 |

**Task list**

| **#** | **Task ID** | **Task**                       | **Layer** | **Who**     | **Est. Days** | **Pairs with**    |
| ----- | ----------- | ------------------------------ | --------- | ----------- | ------------- | ----------------- |
| 87    | P11-T1      | Security review                | Backend   | Laravel Dev | 4             | Backend only      |
| 88    | P11-T2      | Query and performance tuning   | Backend   | Laravel Dev | 3             | Backend only      |
| 89    | P11-T3      | Backup, restore and operations | Backend   | Laravel Dev | 3             | Backend only      |
| 91    | P11-T5      | Load test                      | Both      | Both        | 2             | Both repositories |
| 92    | P11-T6      | User acceptance testing        | Both      | Both        | 5             | Both repositories |
| 93    | P11-T7      | Release rehearsal and launch   | Both      | Both        | 2             | Both repositories |

**Task details**

<div class="joplin-table-wrapper"><table><thead><tr><th><p><strong>Task</strong></p></th><th><p><strong>Steps</strong></p></th><th><p><strong>Done when</strong></p></th></tr></thead><tbody><tr><td><p><strong>P11-T1</strong></p><p><strong>Security review</strong></p><p>Backend · 4 d</p></td><td><ul><li>Go through the security checklist: access control, rate limits, uploads, headers, secrets</li><li>Run composer audit and npm audit; fix high findings</li><li>Run an automated scan and a manual IDOR check on every resource</li></ul></td><td><p>Checklist fully ticked with no high-severity findings.</p></td></tr><tr><td><p><strong>P11-T2</strong></p><p><strong>Query and performance tuning</strong></p><p>Backend · 3 d</p></td><td><ul><li>Find N+1 and slow queries with EXPLAIN</li><li>Add or adjust indexes from the key-query list</li><li>Cache heavy endpoints where measured</li></ul></td><td><p>Key queries meet the response-time targets.</p></td></tr><tr><td><p><strong>P11-T3</strong></p><p><strong>Backup, restore and operations</strong></p><p>Backend · 3 d</p></td><td><ul><li>Automate the daily backup with an off-server copy</li><li>Restore into a test database and time it</li><li>Write the runbook; confirm scheduler, queue worker and monitoring alerts</li></ul></td><td><p>A backup has been restored successfully and the runbook exists.</p></td></tr><tr><td><p><strong>P11-T5</strong></p><p><strong>Load test</strong></p><p>Both · 2 d</p></td><td><ul><li>k6 scripts for login, patient search, booking and a report</li><li>Run with realistic data at 50 concurrent users</li><li>Fix bottlenecks</li></ul></td><td><p>Targets from the non-functional requirements are met.</p></td></tr><tr><td><p><strong>P11-T6</strong></p><p><strong>User acceptance testing</strong></p><p>Both · 5 d</p></td><td><ul><li>Write UAT scripts per role</li><li>Run with real staff and a few patients on staging</li><li>Log, prioritise and fix defects; retest</li></ul></td><td><p>UAT is signed off by the owner.</p></td></tr><tr><td><p><strong>P11-T7</strong></p><p><strong>Release rehearsal and launch</strong></p><p>Both · 2 d</p></td><td><ul><li>Rehearse the release routine on staging, including rollback</li><li>Back up, deploy to production and run smoke tests</li><li>Watch errors for 30 minutes and hold a retrospective</li></ul></td><td><p>Production is live and stable.</p></td></tr></tbody></table></div>

<div class="joplin-table-wrapper"><table><tbody><tr><th><p><strong>Phase 11 gate — all must be true before launch</strong></p><ul><li>UAT is signed off and all critical tests T-01 to T-14 pass.</li><li>Security checklist is fully ticked and a backup restore has been tested.</li><li>Runbook exists and the release routine has been rehearsed on staging.</li><li>All tasks in this file for the phase meet their "Done when" check and CI is green on this repository.</li><li>The phase is deployed to staging and the documentation and OpenAPI file are up to date.</li></ul></th></tr></tbody></table></div>

SECTION 3

**Tests, Notes and Checklists**

**3\. Tests, Notes and Checklists**

**3.1 Critical Tests by Phase**

These 14 tests come from the HMS documentation and must pass before the phase they belong to can close.

| **Test** | **Scenario**                                                                                          | **Phase** | **Task**     |
| -------- | ----------------------------------------------------------------------------------------------------- | --------- | ------------ |
| **T-01** | Two users book the same doctor slot at the same time: one succeeds, the other gets 409 SLOT_TAKEN     | P5        | P5-T1        |
| **T-02** | Unpaid online booking passes the 10-minute hold: it expires and the slot is free                      | P5        | P5-T4        |
| **T-03** | Patient cancels 30 h, 10 h and 1 h before: refunds of 100%, 50% and 0%                                | P5        | P5-T2        |
| **T-04** | Patient opens another patient's invoice, prescription or record by id: 403 or 404                     | P2, P6    | P2-T7, P6-T4 |
| **T-05** | Doctor opens a patient with no care relationship: 403 unless a break-glass reason is given and logged | P6        | P6-T4        |
| **T-06** | Edit an issued invoice: 409 INVOICE_LOCKED                                                            | P4        | P4-T2        |
| **T-07** | Overpayment is rejected; the same payment sent twice with one Idempotency-Key creates one payment     | P4        | P4-T3        |
| **T-08** | Webhook with a bad signature is rejected; a valid webhook sent twice is applied once                  | P4        | P4-T5        |
| **T-09** | Edit a completed consultation: rejected; an addendum is accepted                                      | P6        | P6-T1        |
| **T-10** | Admit two patients to one bed, or one patient twice: second admission rejected                        | P7        | P7-T2        |
| **T-11** | Discharge a 2.2-day stay: bed charge for 3 days added and the bed is available                        | P7        | P7-T3        |
| **T-12** | Deactivate the last Admin: rejected                                                                   | P1        | P1-T5        |
| **T-13** | Refund above the limit requested by an Accountant waits for Admin; the requester cannot approve it    | P4        | P4-T4        |
| **T-14** | Receptionist calls a doctor-only or Admin-only endpoint: 403                                          | P1        | P1-T4        |

**3.2 Questions to Settle Before Dependent Tasks**

| **ID**    | **Question**                                                                | **Needed before** | **Default assumed**                           |
| --------- | --------------------------------------------------------------------------- | ----------------- | --------------------------------------------- |
| **OQ-02** | Which online payment gateway, and must refunds go through it automatically? | P4-T5             | Provider interface; refunds recorded manually |
| **OQ-03** | Currency, tax rate and legal invoice numbering rules?                       | P4-T2             | One currency and tax rate in Settings         |
| **OQ-04** | May patients self-register, or only through the receptionist?               | P1-T3             | Both, with verification                       |
| **OQ-05** | May a doctor see a patient treated by another doctor?                       | P6-T4             | No, except break-glass with a reason          |
| **OQ-08** | Is SMS or WhatsApp required, and with which provider?                       | P8-T2             | Email only; null SMS adapter                  |
| **OQ-10** | Must bills be cleared before discharge?                                     | P7-T3             | Yes; Admin can override with a reason         |
| **OQ-11** | Confirm cancellation refund percentages and cut-off hours                   | P5-T2             | 100% / 50% / 0% at 24 h and 2 h               |
| **OQ-07** | Record retention period under local law                                     | P11-T3            | Nothing is hard-deleted                       |

**3.3 Implementation Notes**

- Stack: Laravel 11, PHP 8.2 or higher, MySQL 8, Sanctum cookie authentication, Redis queue, Pest. Confirm versions against what you install; the documentation does not fix them.
- Never commit real credentials; commit only a sanitised .env.example. Use fake data on local and staging.
- Branching: short-lived feature branch per task, pull request to main, CI must pass before merge. Tag a release at the end of each phase.
- Keep a running list of decisions (ADRs) and open questions; update it when you make or change a decision.

**3.4 Per-Task Checklist**

<div class="joplin-table-wrapper"><table><tbody><tr><th><p><strong>Before you mark any task done</strong></p><ul><li>The "Done when" check passes.</li><li>Tests for the rules and for each role (allowed and forbidden) are written and green.</li><li>Validation messages and error codes match the API documentation.</li><li>Audit entries and notifications work if the task requires them.</li><li>OpenAPI and documentation are updated; CI is green; the change is merged.</li></ul></th></tr></tbody></table></div>