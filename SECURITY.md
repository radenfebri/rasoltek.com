# Security review — 2026-10-04

Scope: this PHP frontend and its local FlyEnv NGINX site. The separate portal, RDASH infrastructure, production hosting and server operating system were not audited.

## Changes applied

- Router dispatch is restricted to named public pages and the two public APIs. Arbitrary PHP includes and recursive `/router` dispatch are blocked.
- The active FlyEnv site rewrite include blocks internal directories, dotfiles, dependency manifests and common backup/database/archive extensions before the PHP handler runs. Apache fallback rules are in `.htaccess`.
- Dynamic pages and APIs send CSP, anti-framing, content-type, referrer and browser-permission headers. PHP errors are logged rather than displayed; the PHP version response header is removed. NGINX version disclosure and directory listings are disabled on this site.
- Domain input must be a bounded string with valid DNS labels. Raw provider messages are not returned to visitors. Domain order URLs use the validated requested domain.
- API request limits use locked storage outside the document root: 20 domain-check requests per minute, and 30 extension-list requests per minute, per peer IP and fixed endpoint identity. Clean URLs, direct PHP URLs and letter-case variations share the same endpoint bucket. Forged `X-Forwarded-For` headers do not bypass this limit.
- Successful extension lists are cached for one hour outside the web root. Pagination has a page limit and time deadline.
- RDASH credentials are sent only to `https://api.rdash.id`. TLS certificates are verified, redirects are disabled and response size is limited to 1 MiB.
- JSON-LD output escapes HTML metacharacters. Frontend domain results use textContent; contact messages are prepared locally using encoded mailto parameters.

## Verified locally

NGINX configuration validation and reload; PHP syntax; HTTP blocks for `.env`, backups, manifests and internal PHP paths; allowed public pages/assets; 405/422/429 API behavior; forged proxy-header throttling.

## Follow-up dependency audit

`npm audit` reports five high-severity entries in the Tailwind 3 build dependency chain stemming from braces <=3.0.3 (GHSA-vfj7-8cjw-p6xm). The upstream advisory lists no patched braces version. This is a development/build dependency, not code executed by the public PHP site. Keep build inputs restricted to trusted project files, and do not deploy `node_modules`. Tailwind 4 migration should be evaluated separately with CSS compatibility checks; forcing a major-version audit fix would change the project's build interface. This alert remains open, not resolved by the runtime protections.

Source: https://github.com/advisories/GHSA-vfj7-8cjw-p6xm

## Production configuration

Use HTTPS and update APP_URL for the live domain. Install equivalent NGINX rules on the production site; `.htaccess` has no effect on NGINX. Do not store public backups in the document root. Keep PHP, NGINX and dependencies patched, and restrict filesystem permissions on `.env` and the private temporary directory.

The application rate limiter is a basic single-server limit, not DDoS protection. Configure rate limiting at the reverse proxy/CDN for production, especially before multiple app instances are used. If a trusted proxy is introduced, configure the real peer address at the server rather than accepting arbitrary proxy headers in PHP.

No claim of complete security or successful live RDASH integration follows from these checks. The contact form opens an email application and does not store messages on the server.
