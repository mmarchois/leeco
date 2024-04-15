<?php

declare(strict_types=1);

namespace App\Domain\Participant\Specification;

use App\Domain\Event\Event;
use App\Domain\Participant\Participant;
use App\Domain\Participant\Repository\ParticipantRepositoryInterface;

final class IsParticipantAlreadyRegistered
{
    public function __construct(
        private readonly ParticipantRepositoryInterface $participantRepository,
    ) {
    }

    public function isSatisfiedBy(Event $event, string $email): bool
    {
        return $this->participantRepository->findOneByEventAndEmail($event, $email) instanceof Participant;
    }
}
