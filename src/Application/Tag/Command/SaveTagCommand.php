<?php

declare(strict_types=1);

namespace App\Application\Tag\Command;

use App\Application\CommandInterface;
use App\Domain\Event\Event;
use App\Domain\Tag\Tag;

final class SaveTagCommand implements CommandInterface
{
    public ?string $title = null;
    public ?\DateTimeInterface $startDate = null;
    public ?\DateTimeInterface $endDate = null;

    public function __construct(
        public readonly Event $event,
        public readonly ?Tag $tag = null,
    ) {
    }

    public static function create(Event $event, Tag $tag): self
    {
        $command = new self($event, $tag);
        $command->title = $tag->getTitle();
        $command->startDate = $tag->getStartDate();
        $command->endDate = $tag->getEndDate();

        return $command;
    }
}
