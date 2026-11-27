# ADR-003: Authentication and Session Strategy (Sanctum SPA Cookie Mode)

- **Status:** Accepted
- **Date:** 2026-11-27
- **Phase:** Phase 1 (`P1-T2`, `P1-T12`)

## Context

MediCore HMS is a modular monolith backend serving a separate first-party single-page web application (`hms-frontend`). Clinical and administrative sessions carry high-privilege permissions (patient medical records, prescriptions, billing, and user role management). Storing long-lived Bearer tokens in browser `localStorage` or `sessionStorage` exposes credentials to XSS exfiltration.

## Decision

We adopt **Laravel Sanctum in stateful SPA cookie mode** for all first-party web clients:

1. **HttpOnly Encrypted Session Cookies:** Authentication state is stored in an `HttpOnly`, `Secure` (in production), `SameSite=Lax` session cookie (`laravel_session`) backed by the `database` session driver. JavaScript cannot read the session identifier.
2. **Double-Submit CSRF Protection:** Before any state-changing request (`POST`, `PUT`, `PATCH`, `DELETE`), clients call `GET /sanctum/csrf-cookie` to receive a readable `XSRF-TOKEN` cookie and send its decoded value back in the `X-XSRF-TOKEN` header.
3. **Cross-Subdomain Deployment Topology:**
   - **Local:** Frontend `http://localhost:3000` and API `http://localhost:8000` with `SESSION_DOMAIN=localhost` and `SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,localhost:8000`.
   - **Staging / Production:** Frontend `https://app.<domain>` and API `https://api.<domain>` sharing parent cookie domain `SESSION_DOMAIN=.<domain>`, `SANCTUM_STATEFUL_DOMAINS=app.<domain>`, `SESSION_SECURE_COOKIE=true`, and CORS `supports_credentials = true` restricted to `FRONTEND_URL`.
4. **Account Lockout & Throttling:** Login attempts are rate-limited (`5/min` per `email|IP`) and trigger a 15-minute account lock (`locked_until`) after 5 consecutive password failures (`BR-USR-03`), returning neutral error responses.

## Consequences

- **Positive:** Eliminates `localStorage` token theft via XSS, enables immediate server-side session revocation on logout or user deactivation, and aligns with standard browser security controls.
- **Negative / Trade-offs:** Frontend and API must share a registrable parent domain (`SESSION_DOMAIN=.<domain>`) in staging/production, and API clients (such as Postman) must supply `Origin`/`Referer` headers and echo `X-XSRF-TOKEN`.