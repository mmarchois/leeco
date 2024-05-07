<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Domain\User\User;

class Event
{
    public function __construct(
        private string $uuid,
        private string $title,
        private \DateTimeInterface $startDate,
        private \DateTimeInterface $endDate,
        private User $owner,
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

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function update(
        string $title,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
    ): void {
        $this->title = $title;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }
}
