<?php

declare(strict_types=1);

namespace App\Domain\Event\Repository;

use App\Domain\Event\Event;

interface EventRepositoryInterface
{
    public function add(Event $event): Event;

    public function findEventsByOwner(string $userUuid, int $pageSize, int $page): array;

    public function findOneByTitleAndOwner(string $title, string $userUuid): ?Event;

    public function findOneByUuid(string $uuid): ?Event;
}
