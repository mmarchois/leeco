<?php

declare(strict_types=1);

namespace App\Application\Participant\View;

final readonly class ParticipantView
{
    public function __construct(
        public string $uuid,
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $accessCode,
        public bool $accessSent,
    ) {
    }
}
