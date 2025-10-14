<?php

namespace Sakhnovkrg\Epay\DTO;

class PaymentRequest
{
    public function __construct(
        public readonly string $invoiceId,
        public readonly int $amount,
        public readonly string $currency,
        public readonly string $terminal,
        public readonly string $backLink,
        public readonly string $postLink,
        public readonly string $failureBackLink = '',
        public readonly string $failurePostLink = '',
        public readonly bool $autoBackLink = false,
        public readonly string $language = 'RU',
        public readonly string $description = '',
        public readonly string $accountId = '',
        public readonly string $phone = '',
        public readonly string $email = '',
        public readonly string $invoiceIdAlt = '',
        public readonly string $name = '',
        public readonly string $data = '',
        public readonly bool $recurrent = false,
        public readonly ?AuthToken $auth = null
    ) {
    }

    public function toArray(): array
    {
        $data = [
            'invoiceId' => $this->invoiceId,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'terminal' => $this->terminal,
            'backLink' => $this->backLink,
            'postLink' => $this->postLink,
            'language' => $this->language,
        ];

        if ($this->failureBackLink !== '') {
            $data['failureBackLink'] = $this->failureBackLink;
        }

        if ($this->failurePostLink !== '') {
            $data['failurePostLink'] = $this->failurePostLink;
        }

        if ($this->autoBackLink) {
            $data['autoBackLink'] = $this->autoBackLink;
        }

        if ($this->description !== '') {
            $data['description'] = $this->description;
        }

        if ($this->accountId !== '') {
            $data['accountId'] = $this->accountId;
        }

        if ($this->phone !== '') {
            $data['phone'] = $this->phone;
        }

        if ($this->email !== '') {
            $data['email'] = $this->email;
        }

        if ($this->invoiceIdAlt !== '') {
            $data['invoiceIdAlt'] = $this->invoiceIdAlt;
        }

        if ($this->name !== '') {
            $data['name'] = $this->name;
        }

        if ($this->data !== '') {
            $data['data'] = $this->data;
        }

        if ($this->recurrent) {
            $data['recurrent'] = $this->recurrent;
        }

        if ($this->auth instanceof AuthToken) {
            $data['auth'] = $this->auth->toArray();
        }

        return $data;
    }
}
