<?php

declare(strict_types=1);

namespace App\Domain\Participant;

use App\Domain\Event\Event;

class Participant
{
    public function __construct(
        private string $uuid,
        private string $firstName,
        private string $lastName,
        private string $email,
        private string $accessCode,
        private \DateTimeInterface $createdAt,
        private Event $event,
        private bool $accessSent = false,
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

    public function getAccessCode(): string
    {
        return $this->accessCode;
    }

    public function isAccessSent(): bool
    {
        return $this->accessSent;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function update(string $firstName, string $lastName, string $email): void
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
    }
}
