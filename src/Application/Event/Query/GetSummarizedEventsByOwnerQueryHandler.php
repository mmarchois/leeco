<?php

declare(strict_types=1);

namespace App\Application\Event\Query;

use App\Domain\Event\Repository\EventRepositoryInterface;
use App\Domain\Pagination;

final class GetSummarizedEventsByOwnerQueryHandler
{
    public function __construct(
        private EventRepositoryInterface $eventRepository,
    ) {
    }

    public function __invoke(GetSummarizedEventsByOwnerQuery $query): Pagination
    {
        ['events' => $events, 'count' => $count] = $this->eventRepository->findEventsByOwner(
            $query->ownerUuid,
            $query->pageSize,
            $query->page,
        );

        return new Pagination($events, $count, $query->page, $query->pageSize);
    }
}
