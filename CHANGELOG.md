# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-04-04

### Added
- Complete API coverage: Orders, Payments, Refunds, Payouts, Tokens, Transfers, Balances
- Domain-oriented request/response DTOs with strong typing
- `ApiClient` with automatic token refresh on 403 errors
- `SdkBuilder` for fluent SDK construction
- Webhook signature verification helper
- Card encryption helper (RSA-OAEP-256)
- Comprehensive exception hierarchy
- PHP 8.3+ with readonly classes, enums, and typed properties
- PSR-4, PSR-7, PSR-17, PSR-18 compatibility
- 282 unit tests with 96% line coverage, 100% type coverage
- 86% mutation testing score
- PHPStan level max with 0 errors
- CI workflow for PHP 8.3, 8.4, 8.5

### Changed
- Rebranded to `nokimaro/liontech-php-sdk`
- Simplified CI workflow to single test job across all PHP versions

[1.0.0]: https://github.com/nokimaro/liontech-php-sdk/releases/tag/v1.0.0
