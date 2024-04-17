<?php

declare(strict_types=1);

namespace App\Application\Event\Query;

use App\Domain\Event\Event;
use App\Domain\Event\Exception\EventNotFoundException;
use App\Domain\Event\Exception\EventNotOwnedByUserException;
use App\Domain\Event\Repository\EventRepositoryInterface;
use App\Domain\Event\Specification\IsEventOwnedByUser;

final readonly class GetEventQueryHandler
{
    public function __construct(
        private EventRepositoryInterface $eventRepository,
        private IsEventOwnedByUser $isEventOwnedByUser,
    ) {
    }

    public function __invoke(GetEventQuery $query): Event
    {
        $event = $this->eventRepository->findOneByUuid($query->eventUuid);

        if (!$event instanceof Event) {
            throw new EventNotFoundException();
        }

        if (!$this->isEventOwnedByUser->isSatisfiedBy($event, $query->userUuid)) {
            throw new EventNotOwnedByUserException();
        }

        return $event;
    }
}
