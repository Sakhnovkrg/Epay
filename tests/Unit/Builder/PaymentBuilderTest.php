<?php

namespace Sakhnovkrg\Epay\Tests\Unit\Builder;

use PHPUnit\Framework\TestCase;
use Sakhnovkrg\Epay\Builder\PaymentBuilder;
use Sakhnovkrg\Epay\Client\OAuthClient;
use Sakhnovkrg\Epay\Config\EpayConfig;
use Sakhnovkrg\Epay\DTO\AuthToken;
use Sakhnovkrg\Epay\DTO\PaymentRequest;
use Sakhnovkrg\Epay\Helper\PaymentHelper;
use Sakhnovkrg\Epay\Validator\PaymentValidator;
use Sakhnovkrg\Epay\Exceptions\ValidationException;

class PaymentBuilderTest extends TestCase
{
    private EpayConfig $config;
    private OAuthClient $oauthClient;
    private PaymentValidator $validator;
    private PaymentBuilder $builder;

    protected function setUp(): void
    {
        $this->config = new EpayConfig(
            clientId: 'test-client',
            clientSecret: 'test-secret',
            terminal: 'test-terminal',
            isTest: true
        );

        $this->oauthClient = $this->createMock(OAuthClient::class);
        $this->validator = new PaymentValidator();

        $this->builder = new PaymentBuilder(
            $this->config,
            $this->oauthClient,
            $this->validator,
            'test-secret-hash'
        );
    }

    private function mockAuthToken(): void
    {
        $authToken = new AuthToken(
            accessToken: 'test-token',
            expiresIn: 3600,
            scope: 'payment',
            tokenType: 'Bearer'
        );

        $this->oauthClient
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($authToken);
    }

    public function testCanBuildPaymentRequestWithRequiredFields(): void
    {
        $this->mockAuthToken();

        $payment = $this->builder
            ->invoiceId(PaymentHelper::formatInvoiceId('123'))
            ->amount(1000)
            ->backLink('https://example.com/success')
            ->postLink('https://example.com/webhook')
            ->description('Test payment')
            ->build();

        $this->assertInstanceOf(PaymentRequest::class, $payment);
        $this->assertEquals('000123', $payment->invoiceId);
        $this->assertEquals(1000, $payment->amount);
    }

    public function testHelperFormatsInvoiceId(): void
    {
        $this->mockAuthToken();

        $payment = $this->builder
            ->invoiceId(PaymentHelper::formatInvoiceId('1'))
            ->amount(500)
            ->backLink('https://example.com/success')
            ->postLink('https://example.com/webhook')
            ->description('Test payment')
            ->build();

        $this->assertEquals('000001', $payment->invoiceId);
    }

    public function testCanSetOptionalFields(): void
    {
        $this->mockAuthToken();

        $payment = $this->builder
            ->invoiceId(PaymentHelper::formatInvoiceId('456'))
            ->amount(2000)
            ->backLink('https://example.com/success')
            ->postLink('https://example.com/webhook')
            ->description('Test payment')
            ->language('EN')
            ->email('user@example.com')
            ->phone('77001234567')
            ->invoiceIdAlt(PaymentHelper::formatInvoiceId('123'))
            ->name('John Doe')
            ->build();

        $this->assertEquals('EN', $payment->language);
        $this->assertEquals('user@example.com', $payment->email);
        $this->assertEquals('77001234567', $payment->phone);
        $this->assertEquals('000123', $payment->invoiceIdAlt);
        $this->assertEquals('John Doe', $payment->name);
    }

    public function testLanguageIsConvertedToUppercase(): void
    {
        $this->mockAuthToken();

        $payment = $this->builder
            ->invoiceId(PaymentHelper::formatInvoiceId('789'))
            ->amount(500)
            ->backLink('https://example.com/success')
            ->postLink('https://example.com/webhook')
            ->description('Test payment')
            ->language('kz')
            ->build();

        $this->assertEquals('KZ', $payment->language);
    }

    public function testThrowsValidationExceptionForInvalidData(): void
    {
        $this->expectException(ValidationException::class);

        $this->builder
            ->invoiceId('')
            ->amount(0)
            ->backLink('invalid-url')
            ->postLink('invalid-url')
            ->build();
    }

    public function testThrowsValidationExceptionForInvalidEmail(): void
    {
        $this->expectException(ValidationException::class);

        $this->builder
            ->invoiceId('123')
            ->amount(1000)
            ->backLink('https://example.com/success')
            ->postLink('https://example.com/webhook')
            ->email('not-an-email')
            ->build();
    }

    public function testFluentInterface(): void
    {
        $result = $this->builder
            ->invoiceId(PaymentHelper::formatInvoiceId('123'))
            ->amount(1000)
            ->backLink('https://example.com/success')
            ->postLink('https://example.com/webhook');

        $this->assertInstanceOf(PaymentBuilder::class, $result);
        $this->assertSame($this->builder, $result);
    }

    /** @dataProvider phoneNormalizationProvider */
    public function testHelperNormalizesPhone(string $input, string $expected): void
    {
        $this->mockAuthToken();

        $payment = $this->builder
            ->invoiceId(PaymentHelper::formatInvoiceId('123'))
            ->amount(1000)
            ->backLink('https://example.com/success')
            ->postLink('https://example.com/webhook')
            ->description('Test payment')
            ->phone(PaymentHelper::normalizePhone($input))
            ->build();

        $this->assertEquals($expected, $payment->phone);
    }

    public static function phoneNormalizationProvider(): array
    {
        return [
            'with spaces' => ['+7 (700) 123-45-67', '77001234567'],
            'with dashes' => ['7-700-123-45-67', '77001234567'],
            'with parentheses' => ['+7(700)1234567', '77001234567'],
            'already normalized' => ['77001234567', '77001234567'],
            'with plus only' => ['+77001234567', '77001234567'],
        ];
    }

    public function testSecretHashPassedToOAuthClient(): void
    {
        $authToken = new AuthToken(
            accessToken: 'test-token',
            expiresIn: 3600,
            scope: 'payment',
            tokenType: 'Bearer'
        );

        $this->oauthClient
            ->expects($this->once())
            ->method('getToken')
            ->with('000100', 500, 'KZT', 'secret-hash-123', 'https://example.com/webhook', '')
            ->willReturn($authToken);

        $builder = new PaymentBuilder(
            $this->config,
            $this->oauthClient,
            $this->validator,
            'secret-hash-123'
        );

        $builder
            ->invoiceId(PaymentHelper::formatInvoiceId('100'))
            ->amount(500)
            ->backLink('https://example.com/success')
            ->postLink('https://example.com/webhook')
            ->description('Test payment')
            ->build();
    }

    public function testAllRemainingOptionalFields(): void
    {
        $this->mockAuthToken();

        $payment = $this->builder
            ->invoiceId(PaymentHelper::formatInvoiceId('999'))
            ->amount(5000)
            ->currency('USD')
            ->backLink('https://example.com/success')
            ->postLink('https://example.com/webhook')
            ->failureBackLink('https://example.com/fail')
            ->failurePostLink('https://example.com/webhook/fail')
            ->description('Order #999')
            ->accountId('acc-123')
            ->data('{"test": true}')
            ->recurrent()
            ->build();

        $this->assertEquals('USD', $payment->currency);
        $this->assertEquals('https://example.com/fail', $payment->failureBackLink);
        $this->assertEquals('https://example.com/webhook/fail', $payment->failurePostLink);
        $this->assertEquals('Order #999', $payment->description);
        $this->assertEquals('acc-123', $payment->accountId);
        $this->assertEquals('{"test": true}', $payment->data);
        $this->assertTrue($payment->recurrent);
    }
}
