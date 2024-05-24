<?php

declare(strict_types=1);

namespace App\Application\Event\Command;

use App\Application\CommandInterface;
use App\Domain\Event\Event;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class SaveEventCommand implements CommandInterface
{
    public ?string $uuid = null;
    public ?string $title = null;
    public ?UploadedFile $file = null;
    public ?\DateTimeInterface $startDate = null;
    public ?\DateTimeInterface $endDate = null;

    public function __construct(
        public readonly string $userUuid,
        public readonly ?Event $event = null,
    ) {
    }

    public static function create(Event $event): self
    {
        $command = new self($event->getOwner()->getUuid(), $event);
        $command->uuid = $event->getUuid();
        $command->title = $event->getTitle();
        $command->startDate = $event->getStartDate();
        $command->endDate = $event->getEndDate();

        return $command;
    }
}
