<?php

declare(strict_types=1);

namespace App\Application\Event\Command;

use App\Application\CommandInterface;

final readonly class DeleteEventCommand implements CommandInterface
{
    public function __construct(
        public string $uuid,
        public string $userUuid,
    ) {
    }
}
