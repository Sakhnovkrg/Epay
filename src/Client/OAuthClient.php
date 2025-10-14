<?php

namespace Sakhnovkrg\Epay\Client;

use Sakhnovkrg\Epay\Config\EpayConfig;
use Sakhnovkrg\Epay\DTO\AuthToken;
use Sakhnovkrg\Epay\Exceptions\AuthenticationException;

class OAuthClient
{
    public function __construct(
        private readonly EpayConfig $config
    ) {
    }

    public function getToken(
        string $invoiceId,
        int $amount,
        string $currency,
        string $secretHash,
        string $postLink = '',
        string $failurePostLink = ''
    ): AuthToken {
        if ($secretHash === '') {
            throw new AuthenticationException('secret_hash является обязательным параметром');
        }

        $fields = [
            'grant_type' => 'client_credentials',
            'scope' => 'webapi usermanagement email_send verification statement statistics payment',
            'client_id' => $this->config->getClientId(),
            'client_secret' => $this->config->getClientSecret(),
            'invoiceID' => $invoiceId,
            'amount' => $amount,
            'currency' => $currency,
            'terminal' => $this->config->getTerminal(),
            'secret_hash' => $secretHash,
        ];

        if ($postLink !== '') {
            $fields['postLink'] = $postLink;
        }

        if ($failurePostLink !== '') {
            $fields['failurePostLink'] = $failurePostLink;
        }

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
            throw new AuthenticationException("Ошибка cURL: {$error}");
        }

        if ($result === false) {
            throw new AuthenticationException("Ошибка выполнения cURL запроса");
        }

        if ($httpCode !== 200) {
            throw new AuthenticationException("Ошибка аутентификации, HTTP код: {$httpCode}");
        }

        $data = json_decode($result, true);

        if (!$data || !isset($data['access_token'])) {
            throw new AuthenticationException('Некорректный ответ при аутентификации');
        }

        return AuthToken::fromArray($data);
    }
}
