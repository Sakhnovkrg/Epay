<?php

namespace Sakhnovkrg\Epay\DTO;

class PaymentResponse
{
    public function __construct(
        public readonly string $code,
        public readonly string $reason,
        public readonly int $reasonCode,
        public readonly array $data = []
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            code: $data['code'] ?? 'unknown',
            reason: $data['reason'] ?? '',
            reasonCode: $data['reasonCode'] ?? 0,
            data: $data
        );
    }

    public function isSuccess(): bool
    {
        return $this->code === 'ok';
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
