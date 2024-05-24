<?php

declare(strict_types=1);

namespace App\Application\Event\Command;

use App\Application\CommandBusInterface;
use App\Application\IdFactoryInterface;
use App\Application\Media\Command\SaveMediaCommand;
use App\Domain\Event\AccessCodeGenerator;
use App\Domain\Event\Event;
use App\Domain\Event\Exception\EventAlreadyExistException;
use App\Domain\Event\Repository\EventRepositoryInterface;
use App\Domain\Event\Specification\IsEventAlreadyExist;
use App\Domain\Media\MediaTypeEnum;
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
        private AccessCodeGenerator $accessCodeGenerator,
        private CommandBusInterface $commandBus,
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

            if ($command->file) {
                $this->handleMedia($event, $command);
            }

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
                accessCode: $this->accessCodeGenerator->generate(),
                startDate: $command->startDate,
                endDate: $command->endDate,
                owner: $user,
            ),
        );

        if ($command->file) {
            $this->handleMedia($event, $command);
        }

        return $event->getUuid();
    }

    private function handleMedia(Event $event, SaveEventCommand $command): void
    {
        $media = $this->commandBus->handle(
            new SaveMediaCommand(
                event: $event,
                file: $command->file,
                type: MediaTypeEnum::EVENT_BANNER->value,
                media: $event->getMedia(), // will be null on event creation
            ),
        );

        $event->updateMedia($media);
    }
}
