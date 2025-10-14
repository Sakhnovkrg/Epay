<?php

namespace Sakhnovkrg\Epay\Tests\Unit\Widget;

use PHPUnit\Framework\TestCase;
use Sakhnovkrg\Epay\Config\EpayConfig;
use Sakhnovkrg\Epay\DTO\PaymentRequest;
use Sakhnovkrg\Epay\Widget\WidgetRenderer;

class WidgetRendererTest extends TestCase
{
    private WidgetRenderer $renderer;
    private EpayConfig $config;

    protected function setUp(): void
    {
        $this->config = new EpayConfig(
            clientId: 'test-client',
            clientSecret: 'test-secret',
            terminal: 'test-terminal',
            isTest: true
        );

        $this->renderer = new WidgetRenderer($this->config);
    }

    private function createPayment(string $terminal = 'test-terminal', string $description = ''): PaymentRequest
    {
        return new PaymentRequest(
            invoiceId: '000123',
            amount: 1000,
            currency: 'KZT',
            terminal: $terminal,
            backLink: 'https://example.com/success',
            postLink: 'https://example.com/webhook',
            description: $description
        );
    }

    public function testRendersScriptTagsWithTestUrl(): void
    {
        $html = $this->renderer->render($this->createPayment());

        $this->assertStringContainsString('<script src="https://test-epay.epayment.kz/payform/payment-api.js"></script>', $html);
        $this->assertStringContainsString('halyk.pay(', $html);
    }

    public function testRendersScriptTagsWithProductionUrl(): void
    {
        $prodConfig = new EpayConfig(
            clientId: 'prod-client',
            clientSecret: 'prod-secret',
            terminal: 'prod-terminal',
            isTest: false
        );

        $prodRenderer = new WidgetRenderer($prodConfig);
        $html = $prodRenderer->render($this->createPayment('prod-terminal'));

        $this->assertStringContainsString('<script src="https://epay.homebank.kz/payform/payment-api.js"></script>', $html);
    }

    public function testIncludesPaymentDataAsJson(): void
    {
        $html = $this->renderer->render($this->createPayment(description: 'Test payment'));

        $this->assertStringContainsString('"invoiceId":"000123"', $html);
        $this->assertStringContainsString('"amount":1000', $html);
        $this->assertStringContainsString('"currency":"KZT"', $html);
        $this->assertStringContainsString('"description":"Test payment"', $html);
    }

    public function testRenderInlineCallsRender(): void
    {
        $payment = $this->createPayment();

        $normalHtml = $this->renderer->render($payment);
        $inlineHtml = $this->renderer->renderInline($payment);

        $this->assertEquals($normalHtml, $inlineHtml);
    }
}
