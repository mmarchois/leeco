<?php

declare(strict_types=1);

namespace App\Application\Participant\Command;

use App\Application\DateUtilsInterface;
use App\Application\IdFactoryInterface;
use App\Domain\Participant\AccessCodeGenerator;
use App\Domain\Participant\Exception\ParticipantAlreadyExistException;
use App\Domain\Participant\Participant;
use App\Domain\Participant\Repository\ParticipantRepositoryInterface;
use App\Domain\Participant\Specification\IsParticipantAlreadyRegistered;

final readonly class SaveParticipantCommandHandler
{
    public function __construct(
        private IdFactoryInterface $idFactory,
        private DateUtilsInterface $dateUtils,
        private ParticipantRepositoryInterface $participantRepository,
        private AccessCodeGenerator $accessCodeGenerator,
        private IsParticipantAlreadyRegistered $isParticipantAlreadyRegistered,
    ) {
    }

    public function __invoke(SaveParticipantCommand $command): string
    {
        $email = trim(strtolower($command->email));

        // Update participant

        if ($command->participant) {
            if ($email !== $command->participant->getEmail()
                && $this->isParticipantAlreadyRegistered->isSatisfiedBy($command->event, $email)
            ) {
                throw new ParticipantAlreadyExistException();
            }

            $command->participant->update(
                $command->firstName,
                $command->lastName,
                $email,
            );

            return $command->participant->getUuid();
        }

        // Create participant

        if ($this->isParticipantAlreadyRegistered->isSatisfiedBy($command->event, $email)) {
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
                event: $command->event,
                accessSent: false,
            ),
        );

        return $participant->getUuid();
    }
}
