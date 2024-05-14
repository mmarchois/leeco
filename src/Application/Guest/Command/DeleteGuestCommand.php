<?php

declare(strict_types=1);

namespace App\Application\Guest\Command;

use App\Application\CommandInterface;
use App\Domain\Event\Event;

final readonly class DeleteGuestCommand implements CommandInterface
{
    public function __construct(
        public Event $event,
        public string $guestUuid,
    ) {
    }
}
