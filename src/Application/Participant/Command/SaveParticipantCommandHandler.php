<?php

declare(strict_types=1);

namespace App\Application\Participant\Command;

use App\Application\CommandInterface;
use App\Application\DateUtilsInterface;
use App\Application\IdFactoryInterface;
use App\Domain\Event\Event;
use App\Domain\Event\Exception\EventNotFoundException;
use App\Domain\Event\Repository\EventRepositoryInterface;
use App\Domain\Participant\AccessCodeGenerator;
use App\Domain\Participant\Exception\ParticipantAlreadyExistException;
use App\Domain\Participant\Participant;
use App\Domain\Participant\Repository\ParticipantRepositoryInterface;
use App\Domain\Participant\Specification\IsParticipantAlreadyRegistered;

final readonly class SaveParticipantCommandHandler implements CommandInterface
{
    public function __construct(
        private IdFactoryInterface $idFactory,
        private DateUtilsInterface $dateUtils,
        private EventRepositoryInterface $eventRepository,
        private ParticipantRepositoryInterface $participantRepository,
        private AccessCodeGenerator $accessCodeGenerator,
        private IsParticipantAlreadyRegistered $isParticipantAlreadyRegistered,
    ) {
    }

    public function __invoke(SaveParticipantCommand $command): string
    {
        $event = $this->eventRepository->findOneByUuid($command->eventUuid);
        if (!$event instanceof Event) {
            throw new EventNotFoundException();
        }

        $email = trim(strtolower($command->email));

        if ($this->isParticipantAlreadyRegistered->isSatisfiedBy($event, $email)) {
            throw new ParticipantAlreadyExistException();
        }

        $participant = $this->participantRepository->add(
            new Participant(
                uuid: $this->idFactory->make(),
                firstName: $command->firstName,
                lastName: $command->lastName,
                email: $email,
                accessCode: $this->accessCodeGenerator->generate(),
                createdAt: $this->dateUtils->getNow(),
                event: $event,
                accessSent: false,
            ),
        );

        return $participant->getUuid();
    }
}
