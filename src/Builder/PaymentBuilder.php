<?php

namespace Sakhnovkrg\Epay\Builder;

use Sakhnovkrg\Epay\Client\OAuthClient;
use Sakhnovkrg\Epay\Config\EpayConfig;
use Sakhnovkrg\Epay\DTO\PaymentRequest;
use Sakhnovkrg\Epay\Validator\PaymentValidator;
use Sakhnovkrg\Epay\Widget\WidgetRenderer;

class PaymentBuilder
{
    private string $invoiceId = '';
    private int $amount = 0;
    private string $currency = 'KZT';
    private string $backLink = '';
    private string $postLink = '';
    private string $failureBackLink = '';
    private string $failurePostLink = '';
    private bool $autoBackLink = false;
    private string $language = 'RU';
    private string $description = '';
    private string $accountId = '';
    private string $phone = '';
    private string $email = '';
    private string $invoiceIdAlt = '';
    private string $name = '';
    private string $data = '';
    private bool $recurrent = false;

    public function __construct(
        private readonly EpayConfig $config,
        private readonly OAuthClient $oauthClient,
        private readonly PaymentValidator $validator,
        private readonly WidgetRenderer $renderer,
        private readonly string $secretHash
    ) {
    }

    public function invoiceId(string $invoiceId): self
    {
        $this->invoiceId = $invoiceId;
        return $this;
    }

    public function amount(int $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function currency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function backLink(string $backLink): self
    {
        $this->backLink = $backLink;
        return $this;
    }

    public function postLink(string $postLink): self
    {
        $this->postLink = $postLink;
        return $this;
    }

    public function failureBackLink(string $failureBackLink): self
    {
        $this->failureBackLink = $failureBackLink;
        return $this;
    }

    public function failurePostLink(string $failurePostLink): self
    {
        $this->failurePostLink = $failurePostLink;
        return $this;
    }

    public function autoBackLink(bool $autoBackLink = true): self
    {
        $this->autoBackLink = $autoBackLink;
        return $this;
    }

    public function language(string $language): self
    {
        $this->language = strtoupper($language);
        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function accountId(string $accountId): self
    {
        $this->accountId = $accountId;
        return $this;
    }

    public function phone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function email(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function invoiceIdAlt(string $invoiceIdAlt): self
    {
        $this->invoiceIdAlt = $invoiceIdAlt;
        return $this;
    }

    public function name(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function data(string $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function recurrent(bool $recurrent = true): self
    {
        $this->recurrent = $recurrent;
        return $this;
    }

    public function build(): PaymentRequest
    {
        $data = [
            'invoiceId' => $this->invoiceId,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'terminal' => $this->config->getTerminal(),
            'backLink' => $this->backLink,
            'postLink' => $this->postLink,
            'failureBackLink' => $this->failureBackLink,
            'failurePostLink' => $this->failurePostLink,
            'language' => $this->language,
            'description' => $this->description,
            'accountId' => $this->accountId,
            'phone' => $this->phone,
            'email' => $this->email,
            'invoiceIdAlt' => $this->invoiceIdAlt,
            'name' => $this->name,
            'data' => $this->data,
            'recurrent' => $this->recurrent,
        ];

        $this->validator->validate($data);

        $token = $this->oauthClient->getToken(
            $this->invoiceId,
            $this->amount,
            $this->currency,
            $this->secretHash,
            $this->postLink,
            $this->failurePostLink
        );

        return new PaymentRequest(
            invoiceId: $this->invoiceId,
            amount: $this->amount,
            currency: $this->currency,
            terminal: $this->config->getTerminal(),
            backLink: $this->backLink,
            postLink: $this->postLink,
            failureBackLink: $this->failureBackLink,
            failurePostLink: $this->failurePostLink,
            autoBackLink: $this->autoBackLink,
            language: $this->language,
            description: $this->description,
            accountId: $this->accountId,
            phone: $this->phone,
            email: $this->email,
            invoiceIdAlt: $this->invoiceIdAlt,
            name: $this->name,
            data: $this->data,
            recurrent: $this->recurrent,
            auth: $token
        );
    }
}
