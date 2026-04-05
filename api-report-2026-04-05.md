# LionTech API — Spec Compliance Report

| | |
|---|---|
| **Date** | 2026-04-05 01:22 UTC |
| **Spec** | API for merchant integration v1.0 |
| **Source** | https://docs.liontechnology.ai/api.json |
| **Fixtures** | sandbox API responses, 14 endpoints covered |
| **Probe run** | 2026-04-05T01:13:10+00:00 |
| **Status** | ❌ 9 actionable issues, ⚠️ 3 conditional, probe: 19 fields tested, 11 actually required |
---

## 1. Request Schema — Required Field Mismatches (Static Analysis)

Discrepancies between the spec's `required` array and what the PHP SDK enforces.
> ⚠️ The spec marks **every** request field as optional (`required: []`) — all SDK-required fields appear here.

| Schema (PHP class) | Field | Spec | PHP SDK enforces | Issue |
| --- | --- | --- | --- | --- |
| `MerchantCreateOrderRequest` → `CreateOrderRequest` | `amount` | optional | required (Money) | ⚠️ SDK requires, spec says optional |
| `MerchantCreateOrderRequest` → `CreateOrderRequest` | `description` | optional | required (string) | ⚠️ SDK requires, spec says optional |
| `MerchantCreatePaymentRequest` → `CreatePaymentRequest` | `amount` | optional | required (Money) | ⚠️ SDK requires, spec says optional |
| `MerchantCreatePaymentRequest` → `CreatePaymentRequest` | `paymentData` | optional | required (PaymentData) | ⚠️ SDK requires, spec says optional |
| `MerchantCreateRefundRequest` → `CreateRefundRequest` | `amount` | optional | required (Money) | ⚠️ SDK requires, spec says optional |
| `MerchantCreateRefundRequest` → `CreateRefundRequest` | `paymentId` | optional | required (string) | ⚠️ SDK requires, spec says optional |
| `MerchantCreatePayoutRequest` → `CreatePayoutRequest` | `amount` | optional | required (Money) | ⚠️ SDK requires, spec says optional |
| `MerchantCreatePayoutRequest` → `CreatePayoutRequest` | `paymentData` | optional | required (PaymentData) | ⚠️ SDK requires, spec says optional |

---

## 2. Required Field Probe — Empirical Results

Each field was probed by sending a real request with that field omitted.
`❌ required` = API returned HTTP 400 when field was absent.
`✅ optional` = API accepted the request without the field.

> ⚠️ **SDK bug detected:** one or more fields are required by the API but the SDK omits them when `null`.

| Request class | Field | Spec says | API verdict | SDK behavior |
| --- | --- | --- | --- | --- |
| `CreateOrderRequest` | `amount` | optional | ❌ required | SDK always sends ✅ |
| `CreateOrderRequest` | `description` | optional | ❌ required | SDK always sends ✅ |
| `CreateOrderRequest` | `autoApprove` | optional | ❌ required | SDK always sends ✅ |
| `CreatePaymentRequest` | `amount` | optional | ❌ required | SDK always sends ✅ |
| `CreatePaymentRequest` | `paymentData` | optional | ❌ required | SDK always sends ✅ |
| `CreatePaymentRequest` | `autoApprove` | optional | ❌ required | SDK always sends ✅ |
| `CreatePayoutRequest` | `amount` | optional | ❌ required | SDK always sends ✅ |
| `CreatePayoutRequest` | `paymentData` | optional | ❌ required | SDK always sends ✅ |
| `CreateRefundRequest` | `amount` | optional | ❌ required | SDK always sends ✅ |
| `CreateRefundRequest` | `paymentId` | optional | ❌ required | SDK always sends ✅ |
| `CreateRefundRequest` | `webhookUrl` | optional | ❌ required | ⚠️ SDK may omit when null |

---

## 3. Conditional Fields (Scenario-Specific)

Defined in the spec and handled by the PHP mapper, but absent from all fixtures because
they require specific conditions not reproducible in the sandbox setup.

| Schema | Field | Spec type | Required condition |
| --- | --- | --- | --- |
| `MerchantPaymentResponse` | `additionalAction` | PaymentAdditionalAction (object) | ⚠️ Requires 3DS challenge flow — no 3DS test card in sandbox config |
| `MerchantPaymentResponse` | `items` | array | ⚠️ Undocumented semantics in spec (`array of {}`) — likely sub-transactions; absent for declined/pending payments |
| `MerchantPaymentResponse` | `rrn` | string | ⚠️ Requires successful bank authorization — sandbox test cards are declined |

---

## Appendix — Coverage

**Fixtures** (real sandbox API responses used for analysis):

- `storage/e2e-fixtures/orders/cancel.json`
- `storage/e2e-fixtures/orders/create.json`
- `storage/e2e-fixtures/orders/create_with_id.json`
- `storage/e2e-fixtures/orders/create_with_urls.json`
- `storage/e2e-fixtures/orders/get.json`
- `storage/e2e-fixtures/orders/get_with_items.json`
- `storage/e2e-fixtures/payments/create.json`
- `storage/e2e-fixtures/payments/create_with_id.json`
- `storage/e2e-fixtures/payments/create_with_urls.json`
- `storage/e2e-fixtures/payments/get.json`
- `storage/e2e-fixtures/payouts/create.json`
- `storage/e2e-fixtures/payouts/create_with_webhook.json`
- `storage/e2e-fixtures/payouts/get.json`
- `storage/e2e-fixtures/balances/list.json`

**Required field probe:** `storage/required-fields-probe.json`
Probed at: 2026-04-05T01:13:10+00:00

