<?php

namespace Sakhnovkrg\Epay\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Sakhnovkrg\Epay\Epay;
use Sakhnovkrg\Epay\Config\EpayConfig;
use Sakhnovkrg\Epay\Builder\PaymentBuilder;
use Sakhnovkrg\Epay\DTO\PaymentRequest;
use Sakhnovkrg\Epay\DTO\AuthToken;

class EpayTest extends TestCase
{
    private Epay $epay;
    private EpayConfig $config;

    protected function setUp(): void
    {
        $this->config = new EpayConfig(
            clientId: 'test-client',
            clientSecret: 'test-secret',
            terminal: 'test-terminal',
            isTest: true
        );

        $this->epay = new Epay($this->config);
    }

    public function testPaymentReturnsPaymentBuilder(): void
    {
        $builder = $this->epay->payment('test-secret-hash');

        $this->assertInstanceOf(PaymentBuilder::class, $builder);
    }

    public function testGetConfigReturnsConfig(): void
    {
        $config = $this->epay->getConfig();

        $this->assertSame($this->config, $config);
    }

    public function testRenderReturnsWidgetHtml(): void
    {
        $payment = new PaymentRequest(
            invoiceId: '000123',
            amount: 1000,
            currency: 'KZT',
            terminal: 'test-terminal',
            backLink: 'https://example.com/success',
            postLink: 'https://example.com/webhook',
            auth: new AuthToken(
                accessToken: 'test-token',
                expiresIn: 3600,
                scope: 'payment',
                tokenType: 'Bearer'
            )
        );

        $html = $this->epay->render($payment);

        $this->assertStringContainsString('<script', $html);
        $this->assertStringContainsString('halyk.pay', $html);
        $this->assertStringContainsString('https://test-epay.epayment.kz/payform/payment-api.js', $html);
    }

    public function testGetPaymentDataReturnsArray(): void
    {
        $payment = new PaymentRequest(
            invoiceId: '000123',
            amount: 1000,
            currency: 'KZT',
            terminal: 'test-terminal',
            backLink: 'https://example.com/success',
            postLink: 'https://example.com/webhook',
            description: 'Test payment',
            auth: new AuthToken(
                accessToken: 'test-token',
                expiresIn: 3600,
                scope: 'payment',
                tokenType: 'Bearer'
            )
        );

        $data = $this->epay->getPaymentData($payment);

        $this->assertIsArray($data);
        $this->assertEquals('000123', $data['invoiceId']);
        $this->assertEquals(1000, $data['amount']);
        $this->assertEquals('KZT', $data['currency']);
        $this->assertEquals('test-terminal', $data['terminal']);
        $this->assertEquals('Test payment', $data['description']);
        $this->assertArrayHasKey('auth', $data);
    }

    public function testGetWidgetUrlReturnsCorrectUrl(): void
    {
        $url = $this->epay->getWidgetUrl();

        $this->assertEquals('https://test-epay.epayment.kz/payform/payment-api.js', $url);
    }
}
