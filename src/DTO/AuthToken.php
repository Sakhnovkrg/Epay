<?php

namespace Sakhnovkrg\Epay\DTO;

class AuthToken
{
    public function __construct(
        public readonly string $accessToken,
        public readonly int $expiresIn,
        public readonly string $scope,
        public readonly string $tokenType,
        public readonly string $refreshToken = ''
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            accessToken: $data['access_token'],
            expiresIn: $data['expires_in'],
            scope: $data['scope'],
            tokenType: $data['token_type'],
            refreshToken: $data['refresh_token'] ?? ''
        );
    }

    public function toArray(): array
    {
        return [
            'access_token' => $this->accessToken,
            'expires_in' => $this->expiresIn,
            'scope' => $this->scope,
            'token_type' => $this->tokenType,
            'refresh_token' => $this->refreshToken,
        ];
    }
}
