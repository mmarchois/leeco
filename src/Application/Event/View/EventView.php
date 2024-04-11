<?php

declare(strict_types=1);

namespace App\Application\Event\View;

final readonly class EventView
{
    public function __construct(
        public string $uuid,
        public string $title,
        public \DateTimeInterface $date,
    ) {
    }
}
