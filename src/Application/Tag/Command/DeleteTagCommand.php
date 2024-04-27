<?php

declare(strict_types=1);

namespace App\Application\Tag\Command;

use App\Application\CommandInterface;
use App\Domain\Event\Event;

final class DeleteTagCommand implements CommandInterface
{
    public function __construct(
        public readonly Event $event,
        public readonly string $uuid,
    ) {
    }
}
