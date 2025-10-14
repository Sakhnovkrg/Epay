<?php

namespace Sakhnovkrg\Epay\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use Sakhnovkrg\Epay\Exceptions\EpayException;
use Sakhnovkrg\Epay\Exceptions\AuthenticationException;
use Sakhnovkrg\Epay\Exceptions\ValidationException;

class ExceptionsTest extends TestCase
{
    public function testEpayExceptionIsThrowable(): void
    {
        $exception = new EpayException('Test error');

        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertEquals('Test error', $exception->getMessage());
    }

    public function testAuthenticationExceptionExtendsEpayException(): void
    {
        $exception = new AuthenticationException('Auth failed');

        $this->assertInstanceOf(EpayException::class, $exception);
        $this->assertEquals('Auth failed', $exception->getMessage());
    }

    public function testValidationExceptionExtendsEpayException(): void
    {
        $errors = [
            'email' => 'Invalid email',
            'phone' => 'Invalid phone',
        ];
        $exception = new ValidationException($errors, 'Validation failed');

        $this->assertInstanceOf(EpayException::class, $exception);
        $this->assertEquals('Validation failed', $exception->getMessage());
        $this->assertEquals($errors, $exception->getErrors());
    }
}
