<?php

declare(strict_types=1);

namespace App\Application\Event\Query;

use App\Application\Event\View\EventView;
use App\Domain\Event\Exception\EventNotFoundException;
use App\Domain\Event\Repository\EventRepositoryInterface;

final class GetEventByOwnerQueryHandler
{
    public function __construct(
        private EventRepositoryInterface $eventRepository,
    ) {
    }

    public function __invoke(GetEventByOwnerQuery $query): EventView
    {
        $event = $this->eventRepository->findOneByUuidAndOwner(
            $query->eventUuid,
            $query->ownerUuid,
        );

        if (!$event) {
            throw new EventNotFoundException();
        }

        return $event;
    }
}
