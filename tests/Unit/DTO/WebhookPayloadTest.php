<?php

namespace Sakhnovkrg\Epay\Tests\Unit\DTO;

use PHPUnit\Framework\TestCase;
use Sakhnovkrg\Epay\DTO\WebhookPayload;

class WebhookPayloadTest extends TestCase
{
    public function testCanCreateFromArray(): void
    {
        $data = [
            'code' => 'ok',
            'invoiceId' => '000123',
            'amount' => 1000.50,
            'currency' => 'KZT',
            'approvalCode' => 'ABC123',
            'cardId' => 'card-123',
            'cardMask' => '440564******5096',
            'cardType' => 'VISA',
            'email' => 'user@example.com',
            'phone' => '77001234567',
            'name' => 'Test User',
            'secure' => 'yes',
            'ip' => '192.168.1.1',
            'ipCountry' => 'KZ',
            'dateTime' => '2024-01-01 12:00:00',
        ];

        $webhook = WebhookPayload::fromArray($data);

        $this->assertEquals('ok', $webhook->code);
        $this->assertEquals('000123', $webhook->invoiceId);
        $this->assertEquals(1000.50, $webhook->amount);
        $this->assertEquals('card-123', $webhook->cardId);
        $this->assertEquals('user@example.com', $webhook->email);
    }

    public function testIsSuccess(): void
    {
        $success = WebhookPayload::fromArray(['code' => 'ok']);
        $this->assertTrue($success->isSuccess());

        $error = WebhookPayload::fromArray(['code' => 'error']);
        $this->assertFalse($error->isSuccess());
    }

    public function testIsSecure(): void
    {
        $secure = WebhookPayload::fromArray(['secure' => 'yes']);
        $this->assertTrue($secure->isSecure());

        $notSecure = WebhookPayload::fromArray(['secure' => 'no']);
        $this->assertFalse($notSecure->isSecure());
    }

    public function testHandlesMissingFieldsWithDefaults(): void
    {
        $webhook = WebhookPayload::fromArray(['code' => 'ok']);

        $this->assertEquals('', $webhook->invoiceId);
        $this->assertEquals(0.0, $webhook->amount);
        $this->assertEquals('', $webhook->email);
        $this->assertEquals(0.0, $webhook->ipLatitude);
        $this->assertEquals(0.0, $webhook->ipLongitude);
    }

    public function testRoundTripConversion(): void
    {
        $original = [
            'code' => 'ok',
            'invoiceId' => '123',
            'amount' => 500.0,
            'currency' => 'KZT',
            'cardMask' => '440564******5096',
        ];

        $webhook = WebhookPayload::fromArray($original);
        $array = $webhook->toArray();

        $this->assertEquals('ok', $array['code']);
        $this->assertEquals('123', $array['invoiceId']);
        $this->assertEquals(500.0, $array['amount']);
        $this->assertEquals('KZT', $array['currency']);
        $this->assertEquals('440564******5096', $array['cardMask']);
    }
}
