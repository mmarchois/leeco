<?php

declare(strict_types=1);

namespace App\Application\Guest\Query;

use App\Application\QueryInterface;

final readonly class GetGuestsByEventQuery implements QueryInterface
{
    public function __construct(
        public string $eventUuid,
        public int $page,
        public int $pageSize,
    ) {
    }
}
