<?php

declare(strict_types=1);

namespace App\Domain\Guest;

use App\Domain\Event\Event;

class Guest
{
    public function __construct(
        private string $uuid,
        private string $firstName,
        private string $lastName,
        private string $deviceIdentifier,
        private \DateTimeInterface $createdAt,
        private Event $event,
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

    public function getDeviceIdentifier(): string
    {
        return $this->deviceIdentifier;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }
}
