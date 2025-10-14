<?php

namespace Sakhnovkrg\Epay\DTO;

class WebhookFailure
{
    public function __construct(
        public readonly string $id,
        public readonly string $dateTime,
        public readonly string $invoiceId,
        public readonly string $invoiceIdAlt,
        public readonly float $amount,
        public readonly string $currency,
        public readonly string $terminal,
        public readonly string $accountId,
        public readonly string $description,
        public readonly string $language,
        public readonly string $cardMask,
        public readonly string $cardType,
        public readonly string $issuer,
        public readonly string $reference,
        public readonly string $secure,
        public readonly string $secureDetails,
        public readonly string $tokenRecipient,
        public readonly string $code,
        public readonly string $reason,
        public readonly int $reasonCode,
        public readonly string $name,
        public readonly string $email,
        public readonly string $phone,
        public readonly string $ip,
        public readonly string $ipCountry,
        public readonly string $ipCity,
        public readonly string $ipRegion,
        public readonly string $ipDistrict,
        public readonly float $ipLongitude,
        public readonly float $ipLatitude
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            dateTime: $data['dateTime'] ?? '',
            invoiceId: $data['invoiceId'] ?? '',
            invoiceIdAlt: $data['invoiceIdAlt'] ?? '',
            amount: (float) ($data['amount'] ?? 0),
            currency: $data['currency'] ?? '',
            terminal: $data['terminal'] ?? '',
            accountId: $data['accountId'] ?? '',
            description: $data['description'] ?? '',
            language: $data['language'] ?? '',
            cardMask: $data['cardMask'] ?? '',
            cardType: $data['cardType'] ?? '',
            issuer: $data['issuer'] ?? '',
            reference: $data['reference'] ?? '',
            secure: $data['secure'] ?? '',
            secureDetails: $data['secureDetails'] ?? '',
            tokenRecipient: $data['tokenRecipient'] ?? '',
            code: $data['code'] ?? '',
            reason: $data['reason'] ?? '',
            reasonCode: (int) ($data['reasonCode'] ?? 0),
            name: $data['name'] ?? '',
            email: $data['email'] ?? '',
            phone: $data['phone'] ?? '',
            ip: $data['ip'] ?? '',
            ipCountry: $data['ipCountry'] ?? '',
            ipCity: $data['ipCity'] ?? '',
            ipRegion: $data['ipRegion'] ?? '',
            ipDistrict: $data['ipDistrict'] ?? '',
            ipLongitude: (float) ($data['ipLongitude'] ?? 0),
            ipLatitude: (float) ($data['ipLatitude'] ?? 0)
        );
    }

    public function isError(): bool
    {
        return $this->code === 'error';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'dateTime' => $this->dateTime,
            'invoiceId' => $this->invoiceId,
            'invoiceIdAlt' => $this->invoiceIdAlt,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'terminal' => $this->terminal,
            'accountId' => $this->accountId,
            'description' => $this->description,
            'language' => $this->language,
            'cardMask' => $this->cardMask,
            'cardType' => $this->cardType,
            'issuer' => $this->issuer,
            'reference' => $this->reference,
            'secure' => $this->secure,
            'secureDetails' => $this->secureDetails,
            'tokenRecipient' => $this->tokenRecipient,
            'code' => $this->code,
            'reason' => $this->reason,
            'reasonCode' => $this->reasonCode,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'ip' => $this->ip,
            'ipCountry' => $this->ipCountry,
            'ipCity' => $this->ipCity,
            'ipRegion' => $this->ipRegion,
            'ipDistrict' => $this->ipDistrict,
            'ipLongitude' => $this->ipLongitude,
            'ipLatitude' => $this->ipLatitude,
        ];
    }
}
