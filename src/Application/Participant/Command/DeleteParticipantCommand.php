<?php

declare(strict_types=1);

namespace App\Application\Participant\Command;

use App\Application\CommandInterface;
use App\Domain\Event\Event;

final readonly class DeleteParticipantCommand implements CommandInterface
{
    public function __construct(
        public Event $event,
        public string $participantUuid,
    ) {
    }
}
