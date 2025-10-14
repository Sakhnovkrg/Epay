<?php

namespace Sakhnovkrg\Epay\Tests\Unit\Validator;

use PHPUnit\Framework\TestCase;
use Sakhnovkrg\Epay\Validator\PaymentValidator;
use Sakhnovkrg\Epay\Exceptions\ValidationException;

class PaymentValidatorTest extends TestCase
{
    private PaymentValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new PaymentValidator();
    }

    private function validData(array $overrides = []): array
    {
        return array_merge([
            'invoiceId' => '000123',
            'amount' => 1000,
            'currency' => 'KZT',
            'terminal' => 'test-terminal',
            'backLink' => 'https://example.com/success',
            'postLink' => 'https://example.com/webhook',
            'description' => 'Test payment',
        ], $overrides);
    }

    public function testValidatesSuccessfullyWithValidData(): void
    {
        $this->validator->validate($this->validData());
        $this->expectNotToPerformAssertions();
    }

    /** @dataProvider invalidInvoiceIdProvider */
    public function testThrowsExceptionForInvalidInvoiceId(mixed $invoiceId): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate($this->validData(['invoiceId' => $invoiceId]));
    }

    public static function invalidInvoiceIdProvider(): array
    {
        return [
            'empty' => [''],
            'too short' => ['12345'],
            'too long' => ['1234567890123456'],
            'not numeric' => ['abc123'],
        ];
    }

    /** @dataProvider invalidAmountProvider */
    public function testThrowsExceptionForInvalidAmount(mixed $amount): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate($this->validData(['amount' => $amount]));
    }

    public static function invalidAmountProvider(): array
    {
        return [
            'empty' => [''],
            'zero' => [0],
            'negative' => [-100],
        ];
    }

    /** @dataProvider invalidUrlProvider */
    public function testThrowsExceptionForInvalidUrls(string $field, mixed $value): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate($this->validData([$field => $value]));
    }

    public static function invalidUrlProvider(): array
    {
        return [
            'invalid backLink' => ['backLink', 'not-a-url'],
            'invalid postLink' => ['postLink', 'not-a-url'],
        ];
    }

    /** @dataProvider missingRequiredFieldProvider */
    public function testThrowsExceptionForMissingRequiredField(string $field): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate($this->validData([$field => '']));
    }

    public static function missingRequiredFieldProvider(): array
    {
        return [
            'missing currency' => ['currency'],
            'missing terminal' => ['terminal'],
            'missing backLink' => ['backLink'],
            'missing postLink' => ['postLink'],
        ];
    }

    public function testThrowsExceptionForInvalidEmail(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate($this->validData(['email' => 'not-an-email']));
    }

    /** @dataProvider invalidPhoneProvider */
    public function testThrowsExceptionForInvalidPhone(string $phone): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate($this->validData(['phone' => $phone]));
    }

    public static function invalidPhoneProvider(): array
    {
        return [
            'contains letters' => ['7700abc1234'],
            'contains spaces' => ['7 700 123 4567'],
            'too short' => ['770012345'],
            'too long' => ['770012345678'],
            'starts with 8' => ['87001234567'],
            'no country code' => ['7001234567'],
        ];
    }

    public function testValidatesOptionalFields(): void
    {
        // Valid phone
        $this->validator->validate($this->validData(['phone' => '77001234567']));

        // Valid languages
        $this->validator->validate($this->validData(['language' => 'RU']));
        $this->validator->validate($this->validData(['language' => 'KZ']));
        $this->validator->validate($this->validData(['language' => 'EN']));

        $this->expectNotToPerformAssertions();
    }

    /** @dataProvider invalidOptionalUrlProvider */
    public function testThrowsExceptionForInvalidOptionalUrls(string $field): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate($this->validData([$field => 'not-a-url']));
    }

    public static function invalidOptionalUrlProvider(): array
    {
        return [
            'invalid failureBackLink' => ['failureBackLink'],
            'invalid failurePostLink' => ['failurePostLink'],
        ];
    }

    public function testThrowsExceptionForInvalidLanguage(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate($this->validData(['language' => 'FR']));
    }

    public function testThrowsExceptionWhenDescriptionTooLong(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate($this->validData([
            'description' => str_repeat('a', 126),
        ]));
    }

    public function testValidationExceptionContainsAllErrors(): void
    {
        try {
            $this->validator->validate([
                'invoiceId' => '',
                'amount' => -100,
                'currency' => '',
                'terminal' => '',
                'backLink' => 'invalid',
                'postLink' => 'invalid',
                'description' => '',
                'email' => 'not-email',
            ]);

            $this->fail('Expected ValidationException was not thrown');
        } catch (ValidationException $e) {
            $errors = $e->getErrors();
            $this->assertCount(8, $errors);

            $expectedKeys = ['invoiceId', 'amount', 'currency', 'terminal', 'backLink', 'postLink', 'description', 'email'];
            foreach ($expectedKeys as $key) {
                $this->assertArrayHasKey($key, $errors);
            }
        }
    }

    /** @dataProvider invalidInvoiceIdAltProvider */
    public function testThrowsExceptionForInvalidInvoiceIdAlt(string $invoiceIdAlt): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate($this->validData(['invoiceIdAlt' => $invoiceIdAlt]));
    }

    public static function invalidInvoiceIdAltProvider(): array
    {
        return [
            'too short' => ['12345'],
            'too long' => ['1234567890123456'],
            'not numeric' => ['abc123'],
            'with spaces' => ['123 456'],
        ];
    }

    public function testValidatesNewOptionalFields(): void
    {
        // Valid invoiceIdAlt
        $this->validator->validate($this->validData(['invoiceIdAlt' => '123456']));

        // Valid name
        $this->validator->validate($this->validData(['name' => 'John Doe']));

        $this->expectNotToPerformAssertions();
    }

    /** @dataProvider invalidNameProvider */
    public function testThrowsExceptionForInvalidName(string $name): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate($this->validData(['name' => $name]));
    }

    public static function invalidNameProvider(): array
    {
        return [
            'with cyrillic' => ['Иван Петров'],
            'with numbers' => ['John123'],
            'with special chars' => ['John@Doe'],
        ];
    }
}
