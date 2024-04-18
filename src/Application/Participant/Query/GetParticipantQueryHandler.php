<?php

declare(strict_types=1);

namespace App\Application\Participant\Query;

use App\Domain\Participant\Exception\ParticipantNotBelongsToEventException;
use App\Domain\Participant\Exception\ParticipantNotFoundException;
use App\Domain\Participant\Participant;
use App\Domain\Participant\Repository\ParticipantRepositoryInterface;

final readonly class GetParticipantQueryHandler
{
    public function __construct(
        private ParticipantRepositoryInterface $participantRepository,
    ) {
    }

    public function __invoke(GetParticipantQuery $command): Participant
    {
        $participant = $this->participantRepository->findOneByUuid($command->uuid);
        if (!$participant instanceof Participant) {
            throw new ParticipantNotFoundException();
        }

        if ($participant->getEvent() !== $command->event) {
            throw new ParticipantNotBelongsToEventException();
        }

        return $participant;
    }
}
