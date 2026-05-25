# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.2.0] - 2026-05-25

### Changed
- Default `baseUrl` switched from `https://api.liontechnology.ai` to `https://api.fusionpayments.io`
- Default `secureUrl` switched from `https://secure.liontechnology.ai` to `https://secure.fusionpayments.io`
- `ClientBuilder::sandbox()` now points to `https://api.sandbox.fusionpayments.io` and `https://secure.sandbox.fusionpayments.io`
- README, examples, and `composer.json` description updated to reflect the FusionPayments brand

### Notes
- Provider rebranded from LionTech to FusionPayments and decommissioned the old domain
- Fully backward-compatible for callers that pass their own `baseUrl`/`secureUrl`
- Package name (`nokimaro/liontech-php-sdk`) and namespace (`Nokimaro\LionTech`) are unchanged in this release

## [1.1.3] - 2026-04-05

### Changed
- Updated `examples/04_webhook_verification.php` to use `WebhookPayload::fromJson()` with typed DTO access
- Updated README webhook section to showcase typed webhook handling with `isSuccessful()` / `isDeclined()` helpers

## [1.1.2] - 2026-04-05

### Added
- `WebhookPayload` DTO — parse raw webhook JSON via `WebhookPayload::fromJson($json)` into typed objects
- `WebhookError` value object with `hasError()` helper for error envelope field
- `WebhookEventType` enum for webhook type discrimination

## [1.1.1] - 2026-04-05

### Added
- `error.details[].description` values are now appended to exception messages for richer diagnostics
  (e.g. `Invalid input parameters. Payment method is not enabled for this merchant site.`)

### Fixed
- `PaymentData::sbp()` now serializes `paymentData.object` as `{}` (empty JSON object) instead of `[]`, fixing Go unmarshal errors on the API gateway

### Removed
- `SbpData` class — replace `PaymentData::sbp(new SbpData(...))` with `PaymentData::sbp()`

## [1.1.0] - 2026-04-05

### Added
- `EncryptionKeyClient` with the correct encryption key endpoint; encryption key retrieval moved out of `SignatureClient`
- `Json::decodeObject()` helper for envelope-wrapped single-object responses

### Changed
- `CreateOrderRequest::$description` is now a **required** constructor parameter — the API returns 400 without it despite being marked optional in the OpenAPI spec
- `CreateRefundRequest::$webhookUrl` is now a **required** constructor parameter — the API returns 400 without it; previously the SDK silently omitted it when `null`

### Fixed
- `ApiExceptionMapper` now correctly unwraps the `{type, object, error}` response envelope before reading error details
- `Transport::get()` now applies auth headers on GET requests (were silently missing)

## [1.0.1] - 2026-04-05

### Added
- `ResponseStatus` value object preserving `changedAt` and `description` fields from the API response; previously only `value` was kept

### Changed
- `PaymentResponse::$paymentMethod` changed from `PaymentData|null` to `?string` per OpenAPI spec
- `PayoutResponse::$paymentMethod` changed from silently-nulling array to `?string` per OpenAPI spec
- `isFinal()` / `isSuccessful()` / `isDeclined()` now delegate through `ResponseStatus`

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

[Unreleased]: https://github.com/nokimaro/liontech-php-sdk/compare/v1.2.0...HEAD
[1.2.0]: https://github.com/nokimaro/liontech-php-sdk/compare/v1.1.3...v1.2.0
[1.1.3]: https://github.com/nokimaro/liontech-php-sdk/compare/v1.1.2...v1.1.3
[1.1.2]: https://github.com/nokimaro/liontech-php-sdk/compare/v1.1.1...v1.1.2
[1.1.1]: https://github.com/nokimaro/liontech-php-sdk/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/nokimaro/liontech-php-sdk/compare/v1.0.1...v1.1.0
[1.0.1]: https://github.com/nokimaro/liontech-php-sdk/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/nokimaro/liontech-php-sdk/releases/tag/v1.0.0
