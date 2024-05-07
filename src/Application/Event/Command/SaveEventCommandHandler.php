<?php

declare(strict_types=1);

namespace App\Application\Event\Command;

use App\Application\IdFactoryInterface;
use App\Domain\Event\Event;
use App\Domain\Event\Exception\EventAlreadyExistException;
use App\Domain\Event\Repository\EventRepositoryInterface;
use App\Domain\Event\Specification\IsEventAlreadyExist;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\User;

final readonly class SaveEventCommandHandler
{
    public function __construct(
        private IdFactoryInterface $idFactory,
        private UserRepositoryInterface $userRepository,
        private EventRepositoryInterface $eventRepository,
        private IsEventAlreadyExist $isEventAlreadyExist,
    ) {
    }

    public function __invoke(SaveEventCommand $command): string
    {
        $title = trim($command->title);

        // Update event

        if ($event = $command->event) {
            if ($title !== $event->getTitle() && $this->isEventAlreadyExist->isSatisfiedBy($command->userUuid, $title)) {
                throw new EventAlreadyExistException();
            }

            $event->update($title, $command->startDate, $command->endDate);

            return $command->uuid;
        }

        // Create event

        if ($this->isEventAlreadyExist->isSatisfiedBy($command->userUuid, $title)) {
            throw new EventAlreadyExistException();
        }

        $user = $this->userRepository->findOneByUuid($command->userUuid);
        if (!$user instanceof User) {
            throw new UserNotFoundException();
        }

        $event = $this->eventRepository->add(
            new Event(
                uuid: $this->idFactory->make(),
                title: $title,
                startDate: $command->startDate,
                endDate: $command->endDate,
                owner: $user,
            ),
        );

        return $event->getUuid();
    }
}
