<?php

namespace Sakhnovkrg\Epay\Tests\Unit\Helper;

use PHPUnit\Framework\TestCase;
use Sakhnovkrg\Epay\Helper\PaymentHelper;

class PaymentHelperTest extends TestCase
{
    /** @dataProvider invoiceIdFormattingProvider */
    public function testFormatInvoiceId(string|int $input, string $expected): void
    {
        $this->assertEquals($expected, PaymentHelper::formatInvoiceId($input));
    }

    public static function invoiceIdFormattingProvider(): array
    {
        return [
            'single digit string' => ['1', '000001'],
            'two digits string' => ['12', '000012'],
            'three digits string' => ['123', '000123'],
            'four digits string' => ['1234', '001234'],
            'five digits string' => ['12345', '012345'],
            'six digits string' => ['123456', '123456'],
            'more than six digits string' => ['1234567', '1234567'],
            'single digit int' => [1, '000001'],
            'two digits int' => [12, '000012'],
            'three digits int' => [123, '000123'],
            'large int' => [1234567, '1234567'],
        ];
    }

    /** @dataProvider phoneNormalizationProvider */
    public function testNormalizePhone(string $input, string $expected): void
    {
        $this->assertEquals($expected, PaymentHelper::normalizePhone($input));
    }

    public static function phoneNormalizationProvider(): array
    {
        return [
            'with spaces' => ['+7 (700) 123-45-67', '77001234567'],
            'with dashes' => ['7-700-123-45-67', '77001234567'],
            'with parentheses' => ['+7(700)1234567', '77001234567'],
            'with plus only' => ['+77001234567', '77001234567'],
            'already normalized' => ['77001234567', '77001234567'],
            'with dots' => ['7.700.123.45.67', '77001234567'],
            'mixed formatting' => ['+7 (700)-123.45.67', '77001234567'],
        ];
    }
}
