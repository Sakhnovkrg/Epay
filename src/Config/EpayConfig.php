<?php

namespace Sakhnovkrg\Epay\Config;

use Sakhnovkrg\Epay\Exceptions\ValidationException;

class EpayConfig
{
    private const TEST_OAUTH_URL = 'https://test-epay-oauth.epayment.kz/oauth2/token';
    private const PROD_OAUTH_URL = 'https://epay-oauth.homebank.kz/oauth2/token';
    private const TEST_WIDGET_URL = 'https://test-epay.epayment.kz/payform/payment-api.js';
    private const PROD_WIDGET_URL = 'https://epay.homebank.kz/payform/payment-api.js';

    public function __construct(
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly string $terminal,
        private readonly bool $isTest = false
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if ($this->clientId === '' || $this->clientId === '0') {
            $errors['clientId'] = 'Client ID обязателен';
        }

        if ($this->clientSecret === '' || $this->clientSecret === '0') {
            $errors['clientSecret'] = 'Client Secret обязателен';
        }

        if ($this->terminal === '' || $this->terminal === '0') {
            $errors['terminal'] = 'Терминал обязателен';
        }

        if ($errors !== []) {
            throw new ValidationException($errors, 'Ошибка конфигурации');
        }
    }

    public function getOAuthUrl(): string
    {
        return $this->isTest ? self::TEST_OAUTH_URL : self::PROD_OAUTH_URL;
    }

    public function getWidgetUrl(): string
    {
        return $this->isTest ? self::TEST_WIDGET_URL : self::PROD_WIDGET_URL;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function getTerminal(): string
    {
        return $this->terminal;
    }

    public function isTest(): bool
    {
        return $this->isTest;
    }

    public function isProduction(): bool
    {
        return !$this->isTest;
    }
}
