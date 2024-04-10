<?php

declare(strict_types=1);

namespace App\Domain\Event\Repository;

interface EventRepositoryInterface
{
    public function findEventsByOwner(string $ownerUuid, int $pageSize, int $page): array;
}
