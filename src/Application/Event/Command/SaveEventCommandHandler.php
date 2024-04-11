<?php

declare(strict_types=1);

namespace App\Application\Event\Command;

use App\Application\CommandInterface;
use App\Application\DateUtilsInterface;
use App\Application\IdFactoryInterface;
use App\Domain\Event\Event;
use App\Domain\Event\Exception\EventAlreadyExistException;
use App\Domain\Event\Exception\EventNotFoundException;
use App\Domain\Event\Repository\EventRepositoryInterface;
use App\Domain\Event\Specification\IsEventAlreadyExist;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\User;

final readonly class SaveEventCommandHandler implements CommandInterface
{
    public function __construct(
        private IdFactoryInterface $idFactory,
        private DateUtilsInterface $dateUtils,
        private UserRepositoryInterface $userRepository,
        private EventRepositoryInterface $eventRepository,
        private IsEventAlreadyExist $isEventAlreadyExist,
    ) {
    }

    public function __invoke(SaveEventCommand $command): string
    {
        $title = trim($command->title);
        $date = $command->date;
        $expirationDate = $this->dateUtils->addDaysToDate($date, 30);

        // Update event
        if ($command->uuid) {
            $event = $this->eventRepository->findOneByUuid($command->uuid);
            if (!$event instanceof Event) {
                throw new EventNotFoundException();
            }

            if ($title !== $event->getTitle() && $this->isEventAlreadyExist->isSatisfiedBy($command->userUuid, $title)) {
                throw new EventAlreadyExistException();
            }

            $event->update($title, $date, $expirationDate);

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
                date: $date,
                expirationDate: $expirationDate,
                owner: $user,
            ),
        );

        return $event->getUuid();
    }
}
