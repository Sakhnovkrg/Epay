<?php

namespace Sakhnovkrg\Epay\Exceptions;

class ValidationException extends EpayException
{
    public function __construct(private readonly array $errors, string $message = 'Validation failed')
    {
        parent::__construct($message);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
