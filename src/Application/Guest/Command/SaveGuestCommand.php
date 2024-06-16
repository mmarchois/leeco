<?php

declare(strict_types=1);

namespace App\Application\Guest\Command;

use App\Application\CommandInterface;

final readonly class SaveGuestCommand implements CommandInterface
{
    public function __construct(
        public string $eventUuid,
        public string $firstName,
        public string $lastName,
        public string $deviceIdentifier,
    ) {
    }
}
