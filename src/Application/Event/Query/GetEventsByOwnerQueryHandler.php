<?php

declare(strict_types=1);

namespace App\Application\Event\Query;

use App\Domain\Event\Repository\EventRepositoryInterface;
use App\Domain\Pagination;

final class GetEventsByOwnerQueryHandler
{
    public function __construct(
        private EventRepositoryInterface $eventRepository,
    ) {
    }

    public function __invoke(GetEventsByOwnerQuery $query): Pagination
    {
        ['events' => $events, 'count' => $count] = $this->eventRepository->findEventsByOwner(
            $query->userUuid,
            $query->pageSize,
            $query->page,
        );

        return new Pagination($events, $count, $query->page, $query->pageSize);
    }
}
