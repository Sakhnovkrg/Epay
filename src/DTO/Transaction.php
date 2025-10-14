<?php

namespace Sakhnovkrg\Epay\DTO;

class Transaction
{
    public function __construct(
        public readonly string $id,
        public readonly string $createdDate,
        public readonly string $invoiceID,
        public readonly float $amount,
        public readonly float $amountBonus,
        public readonly float $payoutAmount,
        public readonly float $orgAmount,
        public readonly string $approvalCode,
        public readonly string $currency,
        public readonly string $terminal,
        public readonly string $terminalID,
        public readonly string $accountID,
        public readonly string $description,
        public readonly string $language,
        public readonly string $cardMask,
        public readonly string $cardType,
        public readonly string $issuer,
        public readonly string $reference,
        public readonly string $reason,
        public readonly string $reasonCode,
        public readonly string $intReference,
        public readonly bool $secure,
        public readonly string $secureDetails,
        public readonly string $statusID,
        public readonly string $statusName,
        public readonly string $name,
        public readonly string $email,
        public readonly string $phone,
        public readonly string $cardID,
        public readonly string $xlsRRN,
        public readonly string $ip,
        public readonly string $ipCountry,
        public readonly string $ipCity,
        public readonly string $ipRegion,
        public readonly string $ipDistrict,
        public readonly float $ipLatitude,
        public readonly float $ipLongitude,
        public readonly string $data = ''
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            createdDate: $data['createdDate'] ?? '',
            invoiceID: $data['invoiceID'] ?? '',
            amount: (float) ($data['amount'] ?? 0),
            amountBonus: (float) ($data['amountBonus'] ?? 0),
            payoutAmount: (float) ($data['payoutAmount'] ?? 0),
            orgAmount: (float) ($data['orgAmount'] ?? 0),
            approvalCode: $data['approvalCode'] ?? '',
            currency: $data['currency'] ?? '',
            terminal: $data['terminal'] ?? '',
            terminalID: $data['terminalID'] ?? '',
            accountID: $data['accountID'] ?? '',
            description: $data['description'] ?? '',
            language: $data['language'] ?? '',
            cardMask: $data['cardMask'] ?? '',
            cardType: $data['cardType'] ?? '',
            issuer: $data['issuer'] ?? '',
            reference: $data['reference'] ?? '',
            reason: $data['reason'] ?? '',
            reasonCode: $data['reasonCode'] ?? '',
            intReference: $data['intReference'] ?? '',
            secure: (bool) ($data['secure'] ?? false),
            secureDetails: $data['secureDetails'] ?? '',
            statusID: $data['statusID'] ?? '',
            statusName: $data['statusName'] ?? '',
            name: $data['name'] ?? '',
            email: $data['email'] ?? '',
            phone: $data['phone'] ?? '',
            cardID: $data['cardID'] ?? '',
            xlsRRN: $data['xlsRRN'] ?? '',
            ip: $data['ip'] ?? '',
            ipCountry: $data['ipCountry'] ?? '',
            ipCity: $data['ipCity'] ?? '',
            ipRegion: $data['ipRegion'] ?? '',
            ipDistrict: $data['ipDistrict'] ?? '',
            ipLatitude: (float) ($data['ipLatitude'] ?? 0),
            ipLongitude: (float) ($data['ipLongitude'] ?? 0),
            data: $data['data'] ?? ''
        );
    }

    public function isRefunded(): bool
    {
        return $this->statusName === 'REFUND';
    }

    public function isAuthorized(): bool
    {
        return $this->statusName === 'AUTH';
    }

    public function isCanceled(): bool
    {
        return $this->statusName === 'CANCEL';
    }

    public function isCharged(): bool
    {
        return $this->statusName === 'CHARGE';
    }

    public function isVerified(): bool
    {
        return $this->statusName === 'VERIFIED';
    }

    public function isFailed(): bool
    {
        return $this->statusName === 'FAILED';
    }

    public function isRejected(): bool
    {
        return $this->statusName === 'REJECT';
    }

    public function isNew(): bool
    {
        return $this->statusName === 'NEW';
    }

    public function isCancelOld(): bool
    {
        return $this->statusName === 'CANCEL_OLD';
    }

    public function isFingerprint(): bool
    {
        return $this->statusName === 'FINGERPRINT';
    }

    public function is3D(): bool
    {
        return $this->statusName === '3D';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'createdDate' => $this->createdDate,
            'invoiceID' => $this->invoiceID,
            'amount' => $this->amount,
            'amountBonus' => $this->amountBonus,
            'payoutAmount' => $this->payoutAmount,
            'orgAmount' => $this->orgAmount,
            'approvalCode' => $this->approvalCode,
            'currency' => $this->currency,
            'terminal' => $this->terminal,
            'terminalID' => $this->terminalID,
            'accountID' => $this->accountID,
            'description' => $this->description,
            'language' => $this->language,
            'cardMask' => $this->cardMask,
            'cardType' => $this->cardType,
            'issuer' => $this->issuer,
            'reference' => $this->reference,
            'reason' => $this->reason,
            'reasonCode' => $this->reasonCode,
            'intReference' => $this->intReference,
            'secure' => $this->secure,
            'secureDetails' => $this->secureDetails,
            'statusID' => $this->statusID,
            'statusName' => $this->statusName,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'cardID' => $this->cardID,
            'xlsRRN' => $this->xlsRRN,
            'ip' => $this->ip,
            'ipCountry' => $this->ipCountry,
            'ipCity' => $this->ipCity,
            'ipRegion' => $this->ipRegion,
            'ipDistrict' => $this->ipDistrict,
            'ipLatitude' => $this->ipLatitude,
            'ipLongitude' => $this->ipLongitude,
            'data' => $this->data,
        ];
    }
}
