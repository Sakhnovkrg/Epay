<?php

namespace Sakhnovkrg\Epay\Client;

use Sakhnovkrg\Epay\Config\EpayConfig;
use Sakhnovkrg\Epay\DTO\TransactionStatus;
use Sakhnovkrg\Epay\Exceptions\EpayException;

class StatusClient
{
    private const TEST_STATUS_URL = 'https://test-epay-api.epayment.kz/check-status/payment/transaction/';
    private const PROD_STATUS_URL = 'https://epay-api.homebank.kz/check-status/payment/transaction/';

    public function __construct(
        private readonly EpayConfig $config,
        private readonly OAuthClient $oauthClient
    ) {
    }

    public function getTransactionStatus(string $invoiceId): TransactionStatus
    {
        $token = $this->getStatusToken();
        $url = $this->getStatusUrl() . $invoiceId;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token->accessToken,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        /** @var string|false $result */
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error !== '' && $error !== '0') {
            throw new EpayException("Ошибка cURL: {$error}");
        }

        if ($result === false) {
            throw new EpayException("Ошибка выполнения cURL запроса");
        }

        if ($httpCode !== 200) {
            throw new EpayException("Ошибка проверки статуса, HTTP код: {$httpCode}");
        }

        $data = json_decode($result, true);

        if (!$data) {
            throw new EpayException('Некорректный ответ при проверке статуса');
        }

        return TransactionStatus::fromArray($data);
    }

    private function getStatusToken(): \stdClass
    {
        $fields = [
            'grant_type' => 'client_credentials',
            'scope' => 'webapi usermanagement email_send verification statement statistics payment',
            'client_id' => $this->config->getClientId(),
            'client_secret' => $this->config->getClientSecret(),
            'terminal' => $this->config->getTerminal(),
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->config->getOAuthUrl());
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        /** @var string|false $result */
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error !== '' && $error !== '0') {
            throw new EpayException("Ошибка cURL: {$error}");
        }

        if ($result === false) {
            throw new EpayException("Ошибка выполнения cURL запроса");
        }

        if ($httpCode !== 200) {
            throw new EpayException("Ошибка аутентификации, HTTP код: {$httpCode}");
        }

        $data = json_decode($result, true);

        if (!$data || !isset($data['access_token'])) {
            throw new EpayException('Некорректный ответ при аутентификации');
        }

        return (object) $data;
    }

    private function getStatusUrl(): string
    {
        return $this->config->isTest() ? self::TEST_STATUS_URL : self::PROD_STATUS_URL;
    }
}
