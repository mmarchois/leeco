<?php

declare(strict_types=1);

namespace App\Application\Event\Command;

use App\Domain\Event\Event;
use App\Domain\Event\Exception\EventNotFoundException;
use App\Domain\Event\Exception\EventNotOwnedByUserException;
use App\Domain\Event\Repository\EventRepositoryInterface;
use App\Domain\Event\Specification\IsEventOwnedByUser;

final readonly class DeleteEventCommandHandler
{
    public function __construct(
        private EventRepositoryInterface $eventRepository,
        private IsEventOwnedByUser $isEventOwnedByUser,
    ) {
    }

    public function __invoke(DeleteEventCommand $command): void
    {
        $event = $this->eventRepository->findOneByUuid($command->uuid);
        if (!$event instanceof Event) {
            throw new EventNotFoundException();
        }

        if (!$this->isEventOwnedByUser->isSatisfiedBy($event, $command->userUuid)) {
            throw new EventNotOwnedByUserException();
        }

        $this->eventRepository->delete($event);
    }
}
