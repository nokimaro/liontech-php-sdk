# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Removed
- `php-http/multipart-stream-builder` production dependency — was never used; all API endpoints use `application/json`

## [1.0.0] - 2026-04-04

### Added
- Complete API coverage: Orders, Payments, Refunds, Payouts, Tokens, Transfers, Balances
- Domain-oriented typed request/response objects (`Requests\*`, `Responses\*`)
- `Http\ApiClient` with automatic token refresh on 403 errors
- `ClientBuilder` for fluent SDK construction with `->sandbox()` shortcut
- `Security\WebhookSignatureVerifier` — webhook signature verification (RSA)
- `Security\CardEncryptor` — card data encryption (RSA-OAEP-256)
- Typed exception hierarchy: `AuthenticationException`, `TokenExpiredException`, `ValidationException`, `ResourceNotFoundException`, `ConflictException`, `RateLimitException`, `TransportException`
- PHP 8.3+ with readonly classes, enums, and typed properties
- PSR-4, PSR-7, PSR-17, PSR-18 compatibility — bring your own HTTP client
- 301 unit tests with 96% line coverage, 100% type coverage
- PHPStan level max with 0 errors
- CI workflow covering PHP 8.3, 8.4, 8.5

[Unreleased]: https://github.com/nokimaro/liontech-php-sdk/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/nokimaro/liontech-php-sdk/releases/tag/v1.0.0
