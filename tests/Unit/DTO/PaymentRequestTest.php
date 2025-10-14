<?php

namespace Sakhnovkrg\Epay\Tests\Unit\DTO;

use PHPUnit\Framework\TestCase;
use Sakhnovkrg\Epay\DTO\PaymentRequest;
use Sakhnovkrg\Epay\DTO\AuthToken;

class PaymentRequestTest extends TestCase
{
    public function testCanCreateWithRequiredFields(): void
    {
        $payment = new PaymentRequest(
            invoiceId: '000123',
            amount: 1000,
            currency: 'KZT',
            terminal: 'test-terminal',
            backLink: 'https://example.com/success',
            postLink: 'https://example.com/webhook'
        );

        $this->assertEquals('000123', $payment->invoiceId);
        $this->assertEquals(1000, $payment->amount);
        $this->assertEquals('KZT', $payment->currency);
        $this->assertEquals('test-terminal', $payment->terminal);
        $this->assertEquals('https://example.com/success', $payment->backLink);
        $this->assertEquals('https://example.com/webhook', $payment->postLink);
        $this->assertEquals('RU', $payment->language);
    }

    public function testCanCreateWithAllFields(): void
    {
        $auth = new AuthToken(
            accessToken: 'test-token',
            expiresIn: 3600,
            scope: 'payment',
            tokenType: 'Bearer'
        );

        $payment = new PaymentRequest(
            invoiceId: '000456',
            amount: 2000,
            currency: 'USD',
            terminal: 'test-terminal',
            backLink: 'https://example.com/success',
            postLink: 'https://example.com/webhook',
            failureBackLink: 'https://example.com/failure',
            failurePostLink: 'https://example.com/webhook/failure',
            language: 'EN',
            description: 'Test payment',
            accountId: 'user-123',
            phone: '77001234567',
            email: 'user@example.com',
            auth: $auth
        );

        $this->assertEquals('000456', $payment->invoiceId);
        $this->assertEquals('EN', $payment->language);
        $this->assertEquals('Test payment', $payment->description);
        $this->assertEquals('user-123', $payment->accountId);
        $this->assertEquals('77001234567', $payment->phone);
        $this->assertEquals('user@example.com', $payment->email);
        $this->assertEquals($auth, $payment->auth);
    }

    public function testToArrayWithRequiredFieldsOnly(): void
    {
        $payment = new PaymentRequest(
            invoiceId: '000123',
            amount: 1000,
            currency: 'KZT',
            terminal: 'test-terminal',
            backLink: 'https://example.com/success',
            postLink: 'https://example.com/webhook'
        );

        $array = $payment->toArray();

        // Обязательные поля
        $this->assertEquals('000123', $array['invoiceId']);
        $this->assertEquals(1000, $array['amount']);
        $this->assertEquals('KZT', $array['currency']);

        // Опциональные поля не должны быть в массиве
        $this->assertArrayNotHasKey('failureBackLink', $array);
        $this->assertArrayNotHasKey('phone', $array);
        $this->assertArrayNotHasKey('invoiceIdAlt', $array);
        $this->assertArrayNotHasKey('recurrent', $array);
    }

    public function testToArrayIncludesAllOptionalFields(): void
    {
        $auth = new AuthToken(
            accessToken: 'test-token',
            expiresIn: 3600,
            scope: 'payment',
            tokenType: 'Bearer'
        );

        $payment = new PaymentRequest(
            invoiceId: '000456',
            amount: 2000,
            currency: 'USD',
            terminal: 'test-terminal',
            backLink: 'https://example.com/success',
            postLink: 'https://example.com/webhook',
            failureBackLink: 'https://example.com/failure',
            failurePostLink: 'https://example.com/webhook/failure',
            language: 'EN',
            description: 'Test payment',
            accountId: 'user-123',
            phone: '77001234567',
            email: 'user@example.com',
            invoiceIdAlt: '000789',
            name: 'John Doe',
            data: '{"custom": "data"}',
            recurrent: true,
            auth: $auth
        );

        $array = $payment->toArray();

        // Проверяем наличие всех опциональных полей
        $this->assertEquals('https://example.com/failure', $array['failureBackLink']);
        $this->assertEquals('77001234567', $array['phone']);
        $this->assertEquals('000789', $array['invoiceIdAlt']);
        $this->assertEquals('John Doe', $array['name']);
        $this->assertTrue($array['recurrent']);
        $this->assertEquals('test-token', $array['auth']['access_token']);
    }

    public function testToArrayExcludesEmptyOptionalFields(): void
    {
        $payment = new PaymentRequest(
            invoiceId: '000123',
            amount: 1000,
            currency: 'KZT',
            terminal: 'test-terminal',
            backLink: 'https://example.com/success',
            postLink: 'https://example.com/webhook',
            failureBackLink: '',
            phone: '',
            invoiceIdAlt: '',
            name: '',
            recurrent: false,
            auth: null
        );

        $array = $payment->toArray();

        $this->assertArrayNotHasKey('failureBackLink', $array);
        $this->assertArrayNotHasKey('phone', $array);
        $this->assertArrayNotHasKey('invoiceIdAlt', $array);
        $this->assertArrayNotHasKey('name', $array);
        $this->assertArrayNotHasKey('recurrent', $array);
        $this->assertArrayNotHasKey('auth', $array);
    }
}
