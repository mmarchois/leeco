<?php

declare(strict_types=1);

namespace App\Domain\Participant\Repository;

interface ParticipantRepositoryInterface
{
    public function findParticipantsByEvent(string $userUuid, string $eventUuid, int $pageSize, int $page): array;
}
