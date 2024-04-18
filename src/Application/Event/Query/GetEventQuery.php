<?php

declare(strict_types=1);

namespace App\Application\Event\Query;

use App\Application\QueryInterface;

final readonly class GetEventQuery implements QueryInterface
{
    public function __construct(
        public string $userUuid,
        public string $eventUuid,
    ) {
    }
}
