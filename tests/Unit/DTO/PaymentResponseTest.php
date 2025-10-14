<?php

namespace Sakhnovkrg\Epay\Tests\Unit\DTO;

use PHPUnit\Framework\TestCase;
use Sakhnovkrg\Epay\DTO\PaymentResponse;

class PaymentResponseTest extends TestCase
{
    public function testCanCreateFromArray(): void
    {
        $data = [
            'code' => 'ok',
            'reason' => 'Payment successful',
            'reasonCode' => 100,
            'invoiceId' => '000123',
        ];

        $response = PaymentResponse::fromArray($data);

        $this->assertEquals('ok', $response->code);
        $this->assertEquals('Payment successful', $response->reason);
        $this->assertEquals(100, $response->reasonCode);
        $this->assertEquals($data, $response->data);
    }

    public function testHandlesMissingFieldsWithDefaults(): void
    {
        $response = PaymentResponse::fromArray([]);

        $this->assertEquals('unknown', $response->code);
        $this->assertEquals('', $response->reason);
        $this->assertEquals(0, $response->reasonCode);
        $this->assertEquals([], $response->data);
    }

    public function testIsSuccess(): void
    {
        $success = PaymentResponse::fromArray(['code' => 'ok']);
        $this->assertTrue($success->isSuccess());

        $error = PaymentResponse::fromArray(['code' => 'error']);
        $this->assertFalse($error->isSuccess());
    }

    public function testToArrayReturnsOriginalData(): void
    {
        $original = [
            'code' => 'ok',
            'reason' => 'Test',
            'reasonCode' => 100,
            'extra' => 'field',
        ];

        $response = PaymentResponse::fromArray($original);
        $this->assertEquals($original, $response->toArray());
    }
}
