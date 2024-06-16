<?php

declare(strict_types=1);

namespace App\Domain\Guest\Specification;

use App\Domain\Guest\Guest;
use App\Domain\Guest\Repository\GuestRepositoryInterface;

final class IsGuestAlreadyExist
{
    public function __construct(
        private readonly GuestRepositoryInterface $guestRepository,
    ) {
    }

    public function isSatisfiedBy(string $deviceIdentifier, string $eventUuid): bool
    {
        return $this->guestRepository->findOneByDeviceIdentifierAndEvent($deviceIdentifier, $eventUuid) instanceof Guest;
    }
}
