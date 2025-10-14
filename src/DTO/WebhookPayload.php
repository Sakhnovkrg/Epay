<?php

namespace Sakhnovkrg\Epay\DTO;

class WebhookPayload
{
    public function __construct(
        public readonly string $accountId,
        public readonly float $amount,
        public readonly string $approvalCode,
        public readonly string $cardId,
        public readonly string $cardMask,
        public readonly string $cardType,
        public readonly string $code,
        public readonly string $currency,
        public readonly string $dateTime,
        public readonly string $description,
        public readonly string $email,
        public readonly string $id,
        public readonly string $invoiceId,
        public readonly string $ip,
        public readonly string $ipCity,
        public readonly string $ipCountry,
        public readonly string $ipDistrict,
        public readonly float $ipLatitude,
        public readonly float $ipLongitude,
        public readonly string $ipRegion,
        public readonly string $issuer,
        public readonly string $language,
        public readonly string $name,
        public readonly string $phone,
        public readonly string $reason,
        public readonly int $reasonCode,
        public readonly string $reference,
        public readonly string $secure,
        public readonly string $secureDetails,
        public readonly string $terminal
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            accountId: $data['accountId'] ?? '',
            amount: (float) ($data['amount'] ?? 0),
            approvalCode: $data['approvalCode'] ?? '',
            cardId: $data['cardId'] ?? '',
            cardMask: $data['cardMask'] ?? '',
            cardType: $data['cardType'] ?? '',
            code: $data['code'] ?? '',
            currency: $data['currency'] ?? '',
            dateTime: $data['dateTime'] ?? '',
            description: $data['description'] ?? '',
            email: $data['email'] ?? '',
            id: $data['id'] ?? '',
            invoiceId: $data['invoiceId'] ?? '',
            ip: $data['ip'] ?? '',
            ipCity: $data['ipCity'] ?? '',
            ipCountry: $data['ipCountry'] ?? '',
            ipDistrict: $data['ipDistrict'] ?? '',
            ipLatitude: (float) ($data['ipLatitude'] ?? 0),
            ipLongitude: (float) ($data['ipLongitude'] ?? 0),
            ipRegion: $data['ipRegion'] ?? '',
            issuer: $data['issuer'] ?? '',
            language: $data['language'] ?? '',
            name: $data['name'] ?? '',
            phone: $data['phone'] ?? '',
            reason: $data['reason'] ?? '',
            reasonCode: (int) ($data['reasonCode'] ?? 0),
            reference: $data['reference'] ?? '',
            secure: $data['secure'] ?? '',
            secureDetails: $data['secureDetails'] ?? '',
            terminal: $data['terminal'] ?? ''
        );
    }

    public function isSuccess(): bool
    {
        return $this->code === 'ok';
    }

    public function isSecure(): bool
    {
        return $this->secure === 'yes';
    }

    public function toArray(): array
    {
        return [
            'accountId' => $this->accountId,
            'amount' => $this->amount,
            'approvalCode' => $this->approvalCode,
            'cardId' => $this->cardId,
            'cardMask' => $this->cardMask,
            'cardType' => $this->cardType,
            'code' => $this->code,
            'currency' => $this->currency,
            'dateTime' => $this->dateTime,
            'description' => $this->description,
            'email' => $this->email,
            'id' => $this->id,
            'invoiceId' => $this->invoiceId,
            'ip' => $this->ip,
            'ipCity' => $this->ipCity,
            'ipCountry' => $this->ipCountry,
            'ipDistrict' => $this->ipDistrict,
            'ipLatitude' => $this->ipLatitude,
            'ipLongitude' => $this->ipLongitude,
            'ipRegion' => $this->ipRegion,
            'issuer' => $this->issuer,
            'language' => $this->language,
            'name' => $this->name,
            'phone' => $this->phone,
            'reason' => $this->reason,
            'reasonCode' => $this->reasonCode,
            'reference' => $this->reference,
            'secure' => $this->secure,
            'secureDetails' => $this->secureDetails,
            'terminal' => $this->terminal,
        ];
    }
}
