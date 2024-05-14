<?php

declare(strict_types=1);

namespace App\Application\Guest\View;

final readonly class GuestView
{
    public function __construct(
        public string $uuid,
        public string $firstName,
        public string $lastName,
        public \DateTimeInterface $createdAt,
    ) {
    }
}
