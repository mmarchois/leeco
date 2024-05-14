<?php

declare(strict_types=1);

namespace App\Application\Guest\Query;

use App\Domain\Guest\Repository\GuestRepositoryInterface;
use App\Domain\Pagination;

final class GetGuestsByEventQueryHandler
{
    public function __construct(
        private GuestRepositoryInterface $guestRepository,
    ) {
    }

    public function __invoke(GetGuestsByEventQuery $query): Pagination
    {
        ['guests' => $guests, 'count' => $count] = $this->guestRepository->findGuestsByEvent(
            $query->eventUuid,
            $query->pageSize,
            $query->page,
        );

        return new Pagination($guests, $count, $query->page, $query->pageSize);
    }
}
