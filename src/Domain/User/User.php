<?php

declare(strict_types=1);

namespace App\Domain\User;

class User
{
    public function __construct(
        private string $uuid,
        private string $firstName,
        private string $lastName,
        private string $email,
        private string $password,
        private bool $isVerified = false,
    ) {
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function verified(): void
    {
        $this->isVerified = true;
    }

    public function updatePassword(string $password): void
    {
        $this->password = $password;
    }
}
