<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Domain\User\User;

class Event
{
    public function __construct(
        private string $uuid,
        private string $title,
        private \DateTimeInterface $date,
        private \DateTimeInterface $expirationDate,
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

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function getExpirationDate(): \DateTimeInterface
    {
        return $this->expirationDate;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function update(
        string $title,
        \DateTimeInterface $date,
        \DateTimeInterface $expirationDate,
    ): void {
        $this->title = $title;
        $this->date = $date;
        $this->expirationDate = $expirationDate;
    }
}
