<?php

declare(strict_types=1);

namespace App\Domain\Guest\Repository;

use App\Domain\Guest\Guest;

interface GuestRepositoryInterface
{
    public function delete(Guest $guest): void;

    public function findGuestsByEvent(string $eventUuid, int $pageSize, int $page): array;

    public function findOneByUuid(string $uuid): ?Guest;
}
