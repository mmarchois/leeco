<?php

declare(strict_types=1);

namespace App\Domain\Event\Specification;

use App\Domain\Event\Event;
use App\Domain\Event\Repository\EventRepositoryInterface;

final class IsEventAlreadyExist
{
    public function __construct(
        private readonly EventRepositoryInterface $eventRepository,
    ) {
    }

    public function isSatisfiedBy(string $userUuid, string $title): bool
    {
        return $this->eventRepository->findEventByTitleAndOwner($title, $userUuid) instanceof Event;
    }
}
