<?php

declare(strict_types=1);

namespace App\Application\Event\Command;

use App\Application\CommandInterface;
use App\Application\Event\View\EventView;

final class SaveEventCommand implements CommandInterface
{
    public ?string $uuid = null;
    public ?string $title = null;
    public ?\DateTimeInterface $date = null;

    public function __construct(
        public readonly string $userUuid,
    ) {
    }

    public static function createFromView(EventView $eventView, string $userUuid): self
    {
        $command = new self($userUuid);
        $command->uuid = $eventView->uuid;
        $command->title = $eventView->title;
        $command->date = $eventView->date;

        return $command;
    }
}
