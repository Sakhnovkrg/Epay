<?php

namespace Sakhnovkrg\Epay\Tests\Unit\DTO;

use PHPUnit\Framework\TestCase;
use Sakhnovkrg\Epay\DTO\Transaction;

class TransactionTest extends TestCase
{
    public function testCanCreateFromArray(): void
    {
        $data = [
            'id' => 'txn-123',
            'invoiceID' => '000123',
            'amount' => 1000.0,
            'statusName' => 'CHARGE',
            'approvalCode' => 'ABC123',
            'cardMask' => '440564******5096',
            'currency' => 'KZT',
        ];

        $transaction = Transaction::fromArray($data);

        $this->assertEquals('txn-123', $transaction->id);
        $this->assertEquals('000123', $transaction->invoiceID);
        $this->assertEquals(1000.0, $transaction->amount);
        $this->assertEquals('CHARGE', $transaction->statusName);
        $this->assertEquals('ABC123', $transaction->approvalCode);
        $this->assertEquals('440564******5096', $transaction->cardMask);
        $this->assertEquals('KZT', $transaction->currency);
    }

    /** @dataProvider statusProvider */
    public function testStatusCheckers(string $status, string $method): void
    {
        $transaction = Transaction::fromArray(['statusName' => $status]);
        $this->assertTrue($transaction->$method());
    }

    public static function statusProvider(): array
    {
        return [
            'CHARGE status' => ['CHARGE', 'isCharged'],
            'REFUND status' => ['REFUND', 'isRefunded'],
            'AUTH status' => ['AUTH', 'isAuthorized'],
            'CANCEL status' => ['CANCEL', 'isCanceled'],
            'VERIFIED status' => ['VERIFIED', 'isVerified'],
            'FAILED status' => ['FAILED', 'isFailed'],
            'REJECT status' => ['REJECT', 'isRejected'],
            'NEW status' => ['NEW', 'isNew'],
            'CANCEL_OLD status' => ['CANCEL_OLD', 'isCancelOld'],
            'FINGERPRINT status' => ['FINGERPRINT', 'isFingerprint'],
            '3D status' => ['3D', 'is3D'],
        ];
    }

    public function testHandlesMissingFieldsWithDefaults(): void
    {
        $transaction = Transaction::fromArray(['statusName' => 'CHARGE']);

        $this->assertEquals('', $transaction->id);
        $this->assertEquals('', $transaction->invoiceID);
        $this->assertEquals(0.0, $transaction->amount);
        $this->assertEquals('', $transaction->approvalCode);
        $this->assertFalse($transaction->secure);
    }

    /** @dataProvider secureFieldProvider */
    public function testParsesSecureField(mixed $input, bool $expected): void
    {
        $transaction = Transaction::fromArray(['secure' => $input]);
        $this->assertEquals($expected, $transaction->secure);
    }

    public static function secureFieldProvider(): array
    {
        return [
            'true boolean' => [true, true],
            'false boolean' => [false, false],
            'string "1"' => ['1', true],
            'string "0"' => ['0', false],
        ];
    }

    public function testCanConvertToArray(): void
    {
        $original = [
            'id' => 'txn-456',
            'statusName' => 'REFUND',
            'amount' => 500.0,
            'invoiceID' => '789',
        ];

        $transaction = Transaction::fromArray($original);
        $array = $transaction->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('txn-456', $array['id']);
        $this->assertEquals('REFUND', $array['statusName']);
        $this->assertEquals(500.0, $array['amount']);
        $this->assertEquals('789', $array['invoiceID']);
    }
}
