<?php

declare(strict_types=1);

namespace App\Domain\Tag\Repository;

use App\Domain\Event\Event;
use App\Domain\Tag\Tag;

interface TagRepositoryInterface
{
    public function add(Tag $tag): Tag;

    public function delete(Tag $tag): void;

    public function findOneByUuid(string $uuid): ?Tag;

    public function findTagsByEvent(string $eventUuid, int $pageSize, int $page): array;

    public function findOneByEventAndTitle(Event $event, string $title): ?Tag;
}
