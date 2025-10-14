<?php

namespace Sakhnovkrg\Epay\Tests\Unit\DTO;

use PHPUnit\Framework\TestCase;
use Sakhnovkrg\Epay\DTO\WebhookFailure;

class WebhookFailureTest extends TestCase
{
    public function testCanCreateFromArray(): void
    {
        $data = [
            'code' => 'error',
            'invoiceId' => '000123',
            'amount' => 1000.0,
            'currency' => 'KZT',
            'reason' => 'Insufficient funds',
            'reasonCode' => 101,
            'cardMask' => '440564******5096',
            'terminal' => 'test-terminal',
        ];

        $failure = WebhookFailure::fromArray($data);

        $this->assertEquals('error', $failure->code);
        $this->assertEquals('000123', $failure->invoiceId);
        $this->assertEquals(1000.0, $failure->amount);
        $this->assertEquals('Insufficient funds', $failure->reason);
        $this->assertEquals(101, $failure->reasonCode);
    }

    public function testIsError(): void
    {
        $error = WebhookFailure::fromArray(['code' => 'error']);
        $this->assertTrue($error->isError());

        $ok = WebhookFailure::fromArray(['code' => 'ok']);
        $this->assertFalse($ok->isError());
    }

    public function testHandlesMissingFieldsWithDefaults(): void
    {
        $failure = WebhookFailure::fromArray(['code' => 'error']);

        $this->assertEquals('', $failure->invoiceId);
        $this->assertEquals(0.0, $failure->amount);
        $this->assertEquals('', $failure->reason);
        $this->assertEquals(0, $failure->reasonCode);
    }

    public function testParsesNumericFieldsCorrectly(): void
    {
        $data = [
            'amount' => '1500.50',
            'reasonCode' => '105',
            'ipLatitude' => '43.238949',
            'ipLongitude' => '76.889709',
        ];

        $failure = WebhookFailure::fromArray($data);

        $this->assertSame(1500.50, $failure->amount);
        $this->assertSame(105, $failure->reasonCode);
        $this->assertSame(43.238949, $failure->ipLatitude);
        $this->assertSame(76.889709, $failure->ipLongitude);
    }

    public function testRoundTripConversion(): void
    {
        $original = [
            'code' => 'error',
            'invoiceId' => '456',
            'amount' => 2500.0,
            'reason' => 'Card blocked',
            'reasonCode' => 102,
        ];

        $failure = WebhookFailure::fromArray($original);
        $array = $failure->toArray();

        $this->assertEquals('error', $array['code']);
        $this->assertEquals('456', $array['invoiceId']);
        $this->assertEquals(2500.0, $array['amount']);
        $this->assertEquals('Card blocked', $array['reason']);
        $this->assertEquals(102, $array['reasonCode']);
    }
}
