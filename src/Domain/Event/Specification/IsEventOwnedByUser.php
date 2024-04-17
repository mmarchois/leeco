<?php

declare(strict_types=1);

namespace App\Domain\Event\Specification;

use App\Domain\Event\Event;

final class IsEventOwnedByUser
{
    public function isSatisfiedBy(Event $event, string $userUuid): bool
    {
        return $event->getOwner()->getUuid() === $userUuid;
    }
}
