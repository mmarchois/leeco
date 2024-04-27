<?php

declare(strict_types=1);

namespace App\Application\Tag\Query;

use App\Application\QueryInterface;
use App\Domain\Event\Event;

final readonly class GetTagQuery implements QueryInterface
{
    public function __construct(
        public Event $event,
        public string $uuid,
    ) {
    }
}
