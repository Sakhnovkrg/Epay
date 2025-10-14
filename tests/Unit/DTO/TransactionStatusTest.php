<?php

namespace Sakhnovkrg\Epay\Tests\Unit\DTO;

use PHPUnit\Framework\TestCase;
use Sakhnovkrg\Epay\DTO\TransactionStatus;
use Sakhnovkrg\Epay\DTO\Transaction;

class TransactionStatusTest extends TestCase
{
    public function testCanCreateFromArrayWithTransaction(): void
    {
        $data = [
            'resultCode' => '100',
            'resultMessage' => 'Success',
            'transaction' => [
                'id' => 'txn-123',
                'invoiceID' => '000123',
                'statusName' => 'CHARGE',
                'amount' => 1000.0,
            ],
        ];

        $status = TransactionStatus::fromArray($data);

        $this->assertEquals('100', $status->resultCode);
        $this->assertEquals('Success', $status->resultMessage);
        $this->assertInstanceOf(Transaction::class, $status->transaction);
        $this->assertEquals('txn-123', $status->transaction->id);
    }

    public function testCanCreateFromArrayWithoutTransaction(): void
    {
        $data = [
            'resultCode' => '102',
            'resultMessage' => 'Invoice not found',
        ];

        $status = TransactionStatus::fromArray($data);

        $this->assertEquals('102', $status->resultCode);
        $this->assertNull($status->transaction);
        $this->assertFalse($status->hasTransaction());
    }

    /** @dataProvider resultCodeProvider */
    public function testResultCodeCheckers(string $code, string $method): void
    {
        $status = TransactionStatus::fromArray([
            'resultCode' => $code,
            'resultMessage' => 'Test',
        ]);

        $this->assertTrue($status->$method());
    }

    public static function resultCodeProvider(): array
    {
        return [
            '100 is success' => ['100', 'isSuccess'],
            '101 is rejected' => ['101', 'isRejected'],
            '102 is not found' => ['102', 'isNotFound'],
            '103 is error' => ['103', 'isError'],
            '104 is terminal absent' => ['104', 'isTerminalAbsent'],
            '106 is incorrect terminal' => ['106', 'isIncorrectTerminal'],
            '107 is in progress' => ['107', 'isInProgress'],
            '109 is terminal not belongs' => ['109', 'isTerminalNotBelongsToClient'],
        ];
    }

    public function testToArrayWithTransaction(): void
    {
        $original = [
            'resultCode' => '100',
            'resultMessage' => 'Success',
            'transaction' => [
                'statusName' => 'CHARGE',
                'amount' => 500.0,
            ],
        ];

        $status = TransactionStatus::fromArray($original);
        $array = $status->toArray();

        $this->assertEquals('100', $array['resultCode']);
        $this->assertEquals('Success', $array['resultMessage']);
        $this->assertIsArray($array['transaction']);
        $this->assertEquals('CHARGE', $array['transaction']['statusName']);
    }

    public function testToArrayWithoutTransaction(): void
    {
        $data = ['resultCode' => '102', 'resultMessage' => 'Not found'];
        $status = TransactionStatus::fromArray($data);
        $array = $status->toArray();

        $this->assertNull($array['transaction']);
    }
}
