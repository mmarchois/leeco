<?php

declare(strict_types=1);

namespace App\Domain\Tag;

use App\Domain\Event\Event;

class Tag
{
    public function __construct(
        private string $uuid,
        private string $title,
        private \DateTimeInterface $startDate,
        private \DateTimeInterface $endDate,
        private Event $event,
    ) {
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getStartDate(): \DateTimeInterface
    {
        return $this->startDate;
    }

    public function getEndDate(): \DateTimeInterface
    {
        return $this->endDate;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }
}
