<?php

declare(strict_types=1);

namespace App\Application\Event\Command;

use App\Application\CommandInterface;

final class SaveEventCommand implements CommandInterface
{
    public ?string $title;
    public ?\DateTimeInterface $date;

    public function __construct(
        public readonly string $userUuid,
    ) {
    }
}
