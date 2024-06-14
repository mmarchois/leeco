<?php

declare(strict_types=1);

namespace App\Domain\Media;

use App\Domain\Event\Event;
use App\Domain\Guest\Guest;

class Media
{
    public function __construct(
        private string $uuid,
        private string $path,
        private string $origin,
        private \DateTimeInterface $createdAt,
        private Event $event,
        private ?Guest $guest = null,
    ) {
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getOrigin(): string
    {
        return $this->origin;
    }

    public function getGuest(): ?Guest
    {
        return $this->guest;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function updatePath(string $path): void
    {
        $this->path = $path;
    }
}
