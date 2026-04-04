# Security Policy

## Supported Versions

| Version | Supported |
|---------|-----------|
| 1.x     | ✅ Yes    |

## Reporting a Vulnerability

**Please do not report security vulnerabilities through public GitHub issues.**

This SDK handles sensitive payment data including card encryption and webhook signature
verification. Security issues must be reported privately.

### How to Report

Use [GitHub Security Advisories](https://github.com/nokimaro/liontech-php-sdk/security/advisories/new)
to submit a vulnerability report privately.

Please include:
- Description of the vulnerability
- Steps to reproduce
- Potential impact
- Suggested fix (if any)

### Response Timeline

| Stage | Timeline |
|-------|----------|
| Acknowledgement | within 48 hours |
| Status update | within 7 days |
| Fix or mitigation | within 30 days for critical issues |

## Scope

**In scope:**

- RSA card data encryption (`Security\CardEncryptor`)
- Webhook signature verification (`Security\WebhookSignatureVerifier`)
- Token handling and storage (`Http\TokenStore`)
- Authentication and exception handling

**Out of scope:**

- Vulnerabilities in LionTech's API itself — report directly to LionTech
- Issues requiring physical access to the server
- Social engineering attacks
- Vulnerabilities in third-party dependencies — report to the respective maintainers

## Disclosure Policy

We follow [Coordinated Vulnerability Disclosure](https://en.wikipedia.org/wiki/Coordinated_vulnerability_disclosure).
Once a fix is released, the vulnerability details will be published in the GitHub Security Advisory.
