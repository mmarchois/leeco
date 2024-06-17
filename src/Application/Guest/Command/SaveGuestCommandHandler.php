<?php

declare(strict_types=1);

namespace App\Application\Guest\Command;

use App\Application\DateUtilsInterface;
use App\Application\IdFactoryInterface;
use App\Domain\Event\Event;
use App\Domain\Event\Exception\EventNotFoundException;
use App\Domain\Event\Repository\EventRepositoryInterface;
use App\Domain\Guest\Exception\GuestAlreadyExistException;
use App\Domain\Guest\Guest;
use App\Domain\Guest\Repository\GuestRepositoryInterface;
use App\Domain\Guest\Specification\IsGuestAlreadyExist;

final readonly class SaveGuestCommandHandler
{
    public function __construct(
        private IdFactoryInterface $idFactory,
        private GuestRepositoryInterface $guestRepository,
        private EventRepositoryInterface $eventRepository,
        private DateUtilsInterface $dateUtils,
        private IsGuestAlreadyExist $isGuestAlreadyExist,
    ) {
    }

    public function __invoke(SaveGuestCommand $command): string
    {
        $event = $this->eventRepository->findOneByUuid($command->eventUuid);
        if (!$event instanceof Event) {
            throw new EventNotFoundException();
        }

        if ($this->isGuestAlreadyExist->isSatisfiedBy($command->deviceIdentifier, $command->eventUuid)) {
            throw new GuestAlreadyExistException();
        }

        $guest = $this->guestRepository->add(
            new Guest(
                uuid: $this->idFactory->make(),
                firstName: $command->firstName,
                lastName: $command->lastName,
                deviceIdentifier: $command->deviceIdentifier,
                createdAt: $this->dateUtils->getNow(),
                event: $event,
            ),
        );

        return $guest->getUuid();
    }
}
