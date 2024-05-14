<?php

declare(strict_types=1);

namespace App\Application\Guest\Command;

use App\Domain\Guest\Exception\GuestNotBelongsToEventException;
use App\Domain\Guest\Exception\GuestNotFoundException;
use App\Domain\Guest\Guest;
use App\Domain\Guest\Repository\GuestRepositoryInterface;

final readonly class DeleteGuestCommandHandler
{
    public function __construct(
        private GuestRepositoryInterface $guestRepository,
    ) {
    }

    public function __invoke(DeleteGuestCommand $command): string
    {
        $guest = $this->guestRepository->findOneByUuid($command->guestUuid);
        if (!$guest instanceof Guest) {
            throw new GuestNotFoundException();
        }

        if ($guest->getEvent() !== $command->event) {
            throw new GuestNotBelongsToEventException();
        }

        $this->guestRepository->delete($guest);

        return $command->guestUuid;
    }
}
