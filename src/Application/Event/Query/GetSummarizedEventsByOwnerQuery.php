<?php

declare(strict_types=1);

namespace App\Application\Event\Query;

use App\Application\QueryInterface;

final readonly class GetSummarizedEventsByOwnerQuery implements QueryInterface
{
    public function __construct(
        public string $ownerUuid,
        public int $page,
        public int $pageSize,
    ) {
    }
}
