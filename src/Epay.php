<?php

namespace Sakhnovkrg\Epay;

use Sakhnovkrg\Epay\Builder\PaymentBuilder;
use Sakhnovkrg\Epay\Client\OAuthClient;
use Sakhnovkrg\Epay\Client\StatusClient;
use Sakhnovkrg\Epay\Config\EpayConfig;
use Sakhnovkrg\Epay\DTO\PaymentRequest;
use Sakhnovkrg\Epay\DTO\TransactionStatus;
use Sakhnovkrg\Epay\Validator\PaymentValidator;
use Sakhnovkrg\Epay\Widget\WidgetRenderer;

class Epay
{
    private readonly OAuthClient $oauthClient;
    private readonly StatusClient $statusClient;
    private readonly PaymentValidator $validator;
    private readonly WidgetRenderer $renderer;

    public function __construct(
        private readonly EpayConfig $config
    ) {
        $this->oauthClient = new OAuthClient($this->config);
        $this->statusClient = new StatusClient($this->config, $this->oauthClient);
        $this->validator = new PaymentValidator();
        $this->renderer = new WidgetRenderer($this->config);
    }

    public function payment(string $secretHash): PaymentBuilder
    {
        return new PaymentBuilder(
            $this->config,
            $this->oauthClient,
            $this->validator,
            $secretHash
        );
    }

    public function render(PaymentRequest $payment): string
    {
        return $this->renderer->render($payment);
    }

    public function getPaymentData(PaymentRequest $payment): array
    {
        return $this->renderer->getPaymentData($payment);
    }

    public function getWidgetUrl(): string
    {
        return $this->renderer->getWidgetUrl();
    }

    public function checkStatus(string $invoiceId): TransactionStatus
    {
        return $this->statusClient->getTransactionStatus($invoiceId);
    }

    public function getConfig(): EpayConfig
    {
        return $this->config;
    }
}
