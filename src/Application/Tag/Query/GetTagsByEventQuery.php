<?php

declare(strict_types=1);

namespace App\Application\Tag\Query;

use App\Application\QueryInterface;

final readonly class GetTagsByEventQuery implements QueryInterface
{
    public function __construct(
        public string $eventUuid,
        public int $page,
        public int $pageSize,
    ) {
    }
}
