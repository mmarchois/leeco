<?php

declare(strict_types=1);

namespace App\Domain\Media;

use App\Domain\Event\Event;

class Media
{
    public function __construct(
        private string $uuid,
        private string $path,
        private string $type,
        private \DateTimeInterface $createdAt,
        private Event $event,
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

    public function getType(): string
    {
        return $this->type;
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
