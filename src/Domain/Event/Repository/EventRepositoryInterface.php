<?php

declare(strict_types=1);

namespace App\Domain\Event\Repository;

use App\Domain\Event\Event;

interface EventRepositoryInterface
{
    public function add(Event $event): Event;

    public function findEventsByOwner(string $ownerUuid, int $pageSize, int $page): array;

    public function findEventByTitleAndOwner(string $title, string $ownerUuid): ?Event;
}
