<?php

declare(strict_types=1);

namespace App\Domain\Event\Repository;

use App\Application\Event\View\EventView;
use App\Domain\Event\Event;

interface EventRepositoryInterface
{
    public function add(Event $event): Event;

    public function findEventsByOwner(string $ownerUuid, int $pageSize, int $page): array;

    public function findOneByTitleAndOwner(string $title, string $ownerUuid): ?Event;

    public function findOneByUuidAndOwner(string $uuid, string $ownerUuid): ?EventView;

    public function findOneByUuid(string $uuid): ?Event;
}
