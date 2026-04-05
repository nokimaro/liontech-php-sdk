<?php

declare(strict_types=1);

use Nokimaro\LionTech\Enums\WebhookEventType;
use Nokimaro\LionTech\Webhooks\WebhookPayload;

const DECLINED_WEBHOOK = '{"type":"PAYMENT","object":{"paymentId":"p-aabbccdd-0000-0000-0000-000000000001","txnId":"1000000000000000001","orderId":"o-aabbccdd-0000-0000-0000-000000000001","autoApprove":true,"webhookUrl":"https://merchant.example.com/webhook","amount":{"value":"10.00","currency":"RUB"},"convAmount":{"value":"10.00","currency":"RUB"},"paymentMethod":"card","createdAt":"2026-01-01T00:00:00.000000Z","status":{"changedAt":"2026-01-01T00:00:01.000000000Z","value":"DECLINED","description":""}},"error":{"code":302,"description":"Multi-factor authentication failed."}}';

const RECONCILED_WEBHOOK = '{"type":"PAYMENT","object":{"paymentId":"p-aabbccdd-0000-0000-0000-000000000002","txnId":"1000000000000000002","orderId":"o-aabbccdd-0000-0000-0000-000000000002","autoApprove":true,"webhookUrl":"https://merchant.example.com/webhook","amount":{"value":"10.00","currency":"RUB"},"convAmount":{"value":"10.00","currency":"RUB"},"paymentMethod":"card","createdAt":"2026-01-01T00:01:00.000000Z","status":{"changedAt":"2026-01-01T00:01:01.000000000Z","value":"RECONCILED","description":""}},"error":{"code":0,"description":"No error."}}';

it('parses declined payment webhook from JSON', function (): void {
    $payload = WebhookPayload::fromJson(DECLINED_WEBHOOK);

    expect($payload->type)
        ->toBe(WebhookEventType::PAYMENT);
    expect($payload->payment->paymentId)
        ->toBe('p-aabbccdd-0000-0000-0000-000000000001');
    expect($payload->payment->status->value)
        ->toBe('DECLINED');
    expect($payload->payment->isDeclined())
        ->toBeTrue();
    expect($payload->payment->isFinal())
        ->toBeTrue();
});

it('parses reconciled payment webhook from JSON', function (): void {
    $payload = WebhookPayload::fromJson(RECONCILED_WEBHOOK);

    expect($payload->type)
        ->toBe(WebhookEventType::PAYMENT);
    expect($payload->payment->paymentId)
        ->toBe('p-aabbccdd-0000-0000-0000-000000000002');
    expect($payload->payment->status->value)
        ->toBe('RECONCILED');
    expect($payload->payment->isSuccessful())
        ->toBeTrue();
    expect($payload->payment->isFinal())
        ->toBeTrue();
});

it('parses webhook error field on declined', function (): void {
    $payload = WebhookPayload::fromJson(DECLINED_WEBHOOK);

    expect($payload->error)
        ->not->toBeNull();
    expect($payload->error?->code)
        ->toBe(302);
    expect($payload->error?->description)
        ->toBe('Multi-factor authentication failed.');
    expect($payload->error?->hasError())
        ->toBeTrue();
});

it('detects no error on reconciled webhook', function (): void {
    $payload = WebhookPayload::fromJson(RECONCILED_WEBHOOK);

    expect($payload->error?->hasError())
        ->toBeFalse();
});

it('parses payment amount and currency', function (): void {
    $payload = WebhookPayload::fromJson(RECONCILED_WEBHOOK);

    expect($payload->payment->amount->amount)
        ->toBe('10.00');
    expect($payload->payment->amount->currency->value)
        ->toBe('RUB');
});

it('parses txnId', function (): void {
    $payload = WebhookPayload::fromJson(RECONCILED_WEBHOOK);

    expect($payload->payment->txnId)
        ->toBe('1000000000000000002');
});

it('throws on invalid JSON', function (): void {
    WebhookPayload::fromJson('not-json');
})->throws(\InvalidArgumentException::class, 'Invalid webhook JSON payload.');
