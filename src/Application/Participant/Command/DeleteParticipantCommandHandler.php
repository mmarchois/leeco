<?php

declare(strict_types=1);

namespace App\Application\Participant\Command;

use App\Application\CommandInterface;
use App\Domain\Participant\Exception\ParticipantNotBelongsToEventException;
use App\Domain\Participant\Exception\ParticipantNotFoundException;
use App\Domain\Participant\Participant;
use App\Domain\Participant\Repository\ParticipantRepositoryInterface;

final readonly class DeleteParticipantCommandHandler implements CommandInterface
{
    public function __construct(
        private ParticipantRepositoryInterface $participantRepository,
    ) {
    }

    public function __invoke(DeleteParticipantCommand $command): string
    {
        $participant = $this->participantRepository->findOneByUuid($command->participantUuid);
        if (!$participant instanceof Participant) {
            throw new ParticipantNotFoundException();
        }

        if ($participant->getEvent() !== $command->event) {
            throw new ParticipantNotBelongsToEventException();
        }

        $this->participantRepository->delete($participant);

        return $command->participantUuid;
    }
}
