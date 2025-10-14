<?php

namespace Sakhnovkrg\Epay\DTO;

class TransactionStatus
{
    public function __construct(
        public readonly string $resultCode,
        public readonly string $resultMessage,
        public readonly ?Transaction $transaction = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        $transaction = null;
        if (isset($data['transaction']) && is_array($data['transaction'])) {
            $transaction = Transaction::fromArray($data['transaction']);
        }

        return new self(
            resultCode: $data['resultCode'],
            resultMessage: $data['resultMessage'],
            transaction: $transaction
        );
    }

    public function isSuccess(): bool
    {
        return $this->resultCode === '100';
    }

    public function isRejected(): bool
    {
        return $this->resultCode === '101';
    }

    public function isInProgress(): bool
    {
        return $this->resultCode === '107';
    }

    public function isNotFound(): bool
    {
        return $this->resultCode === '102';
    }

    public function isError(): bool
    {
        return $this->resultCode === '103';
    }

    public function isTerminalAbsent(): bool
    {
        return $this->resultCode === '104';
    }

    public function isIncorrectTerminal(): bool
    {
        return $this->resultCode === '106';
    }

    public function isTerminalNotBelongsToClient(): bool
    {
        return $this->resultCode === '109';
    }

    public function hasTransaction(): bool
    {
        return $this->transaction instanceof Transaction;
    }

    public function toArray(): array
    {
        return [
            'resultCode' => $this->resultCode,
            'resultMessage' => $this->resultMessage,
            'transaction' => $this->transaction?->toArray(),
        ];
    }
}
