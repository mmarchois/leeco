<?php

declare(strict_types=1);

namespace App\Application\Event\Command;

use App\Application\CommandInterface;
use App\Domain\Event\Event;

final class SaveEventCommand implements CommandInterface
{
    public ?string $uuid = null;
    public ?string $title = null;
    public ?\DateTimeInterface $date = null;

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
        $command->date = $event->getDate();

        return $command;
    }
}
