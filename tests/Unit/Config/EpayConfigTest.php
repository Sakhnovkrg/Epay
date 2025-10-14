<?php

namespace Sakhnovkrg\Epay\Tests\Unit\Config;

use PHPUnit\Framework\TestCase;
use Sakhnovkrg\Epay\Config\EpayConfig;
use Sakhnovkrg\Epay\Exceptions\ValidationException;

class EpayConfigTest extends TestCase
{
    public function testCanCreateConfigWithValidData(): void
    {
        $config = new EpayConfig(
            clientId: 'test-client-id',
            clientSecret: 'test-client-secret',
            terminal: 'test-terminal',
            isTest: true
        );

        $this->assertEquals('test-client-id', $config->getClientId());
        $this->assertEquals('test-client-secret', $config->getClientSecret());
        $this->assertEquals('test-terminal', $config->getTerminal());
        $this->assertTrue($config->isTest());
        $this->assertFalse($config->isProduction());
    }

    public function testCanCreateProductionConfig(): void
    {
        $config = new EpayConfig(
            clientId: 'prod-client-id',
            clientSecret: 'prod-client-secret',
            terminal: 'prod-terminal',
            isTest: false
        );

        $this->assertFalse($config->isTest());
        $this->assertTrue($config->isProduction());
    }

    /** @dataProvider urlProvider */
    public function testReturnsCorrectUrls(bool $isTest, string $expectedOAuth, string $expectedWidget): void
    {
        $config = new EpayConfig(
            clientId: 'test',
            clientSecret: 'test',
            terminal: 'test',
            isTest: $isTest
        );

        $this->assertEquals($expectedOAuth, $config->getOAuthUrl());
        $this->assertEquals($expectedWidget, $config->getWidgetUrl());
    }

    public static function urlProvider(): array
    {
        return [
            'test environment' => [
                true,
                'https://test-epay-oauth.epayment.kz/oauth2/token',
                'https://test-epay.epayment.kz/payform/payment-api.js',
            ],
            'production environment' => [
                false,
                'https://epay-oauth.homebank.kz/oauth2/token',
                'https://epay.homebank.kz/payform/payment-api.js',
            ],
        ];
    }

    /** @dataProvider invalidConfigProvider */
    public function testThrowsExceptionForInvalidConfig(string $clientId, string $clientSecret, string $terminal): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Ошибка конфигурации');

        new EpayConfig(
            clientId: $clientId,
            clientSecret: $clientSecret,
            terminal: $terminal
        );
    }

    public static function invalidConfigProvider(): array
    {
        return [
            'empty client id' => ['', 'test', 'test'],
            'empty client secret' => ['test', '', 'test'],
            'empty terminal' => ['test', 'test', ''],
        ];
    }

    public function testValidationExceptionContainsAllErrors(): void
    {
        try {
            new EpayConfig(clientId: '', clientSecret: '', terminal: '');
            $this->fail('Expected ValidationException was not thrown');
        } catch (ValidationException $e) {
            $errors = $e->getErrors();

            $this->assertCount(3, $errors);
            $this->assertEquals('Client ID обязателен', $errors['clientId']);
            $this->assertEquals('Client Secret обязателен', $errors['clientSecret']);
            $this->assertEquals('Терминал обязателен', $errors['terminal']);
        }
    }
}
