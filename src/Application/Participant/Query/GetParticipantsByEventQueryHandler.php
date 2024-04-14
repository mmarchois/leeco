<?php

declare(strict_types=1);

namespace App\Application\Participant\Query;

use App\Domain\Pagination;
use App\Domain\Participant\Repository\ParticipantRepositoryInterface;

final class GetParticipantsByEventQueryHandler
{
    public function __construct(
        private ParticipantRepositoryInterface $participantRepository,
    ) {
    }

    public function __invoke(GetParticipantsByEventQuery $query): Pagination
    {
        ['participants' => $participants, 'count' => $count] = $this->participantRepository->findParticipantsByEvent(
            $query->userUuid,
            $query->eventUuid,
            $query->pageSize,
            $query->page,
        );

        return new Pagination($participants, $count, $query->page, $query->pageSize);
    }
}
