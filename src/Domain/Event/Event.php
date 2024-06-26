<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Domain\Media\Media;
use App\Domain\User\User;

class Event
{
    public function __construct(
        private string $uuid,
        private string $title,
        private string $accessCode,
        private \DateTimeInterface $startDate,
        private \DateTimeInterface $endDate,
        private User $owner,
        private ?Media $media = null,
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

    public function getAccessCode(): string
    {
        return $this->accessCode;
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

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function updateMedia(Media $media): void
    {
        $this->media = $media;
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
