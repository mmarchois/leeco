<?php

declare(strict_types=1);

namespace App\Domain\Tag\Specification;

use App\Domain\Event\Event;
use App\Domain\Tag\Repository\TagRepositoryInterface;
use App\Domain\Tag\Tag;

final class IsTagAlreadyExist
{
    public function __construct(
        private readonly TagRepositoryInterface $tagRepository,
    ) {
    }

    public function isSatisfiedBy(Event $event, string $title): bool
    {
        return $this->tagRepository->findOneByEventAndTitle($event, $title) instanceof Tag;
    }
}
