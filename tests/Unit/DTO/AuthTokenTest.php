<?php

namespace Sakhnovkrg\Epay\Tests\Unit\DTO;

use PHPUnit\Framework\TestCase;
use Sakhnovkrg\Epay\DTO\AuthToken;

class AuthTokenTest extends TestCase
{
    public function testCanCreateFromArrayWithAllFields(): void
    {
        $data = [
            'access_token' => 'test-token-123',
            'expires_in' => 3600,
            'scope' => 'payment',
            'token_type' => 'Bearer',
            'refresh_token' => 'refresh-123',
        ];

        $token = AuthToken::fromArray($data);

        $this->assertEquals('test-token-123', $token->accessToken);
        $this->assertEquals(3600, $token->expiresIn);
        $this->assertEquals('payment', $token->scope);
        $this->assertEquals('Bearer', $token->tokenType);
        $this->assertEquals('refresh-123', $token->refreshToken);
    }

    public function testRefreshTokenIsOptional(): void
    {
        $data = [
            'access_token' => 'test-token-123',
            'expires_in' => 3600,
            'scope' => 'payment',
            'token_type' => 'Bearer',
        ];

        $token = AuthToken::fromArray($data);

        $this->assertEquals('', $token->refreshToken);
    }

    public function testCanConvertToArray(): void
    {
        $token = new AuthToken(
            accessToken: 'test-token',
            expiresIn: 7200,
            scope: 'payment usermanagement',
            tokenType: 'Bearer',
            refreshToken: 'refresh-token'
        );

        $expected = [
            'access_token' => 'test-token',
            'expires_in' => 7200,
            'scope' => 'payment usermanagement',
            'token_type' => 'Bearer',
            'refresh_token' => 'refresh-token',
        ];

        $this->assertEquals($expected, $token->toArray());
    }
}
