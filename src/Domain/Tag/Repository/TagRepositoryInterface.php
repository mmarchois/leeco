<?php

declare(strict_types=1);

namespace App\Domain\Tag\Repository;

interface TagRepositoryInterface
{
    public function findTagsByEvent(string $eventUuid, int $pageSize, int $page): array;
}
