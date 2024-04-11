<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\App\Event;

use App\Application\Event\Query\GetEventByOwnerQuery;
use App\Application\Event\View\EventView;
use App\Application\QueryBusInterface;
use App\Domain\Event\Exception\EventNotFoundException;
use App\Infrastructure\Security\AuthenticatedUser;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class AbstractEventController
{
    public function __construct(
        protected AuthenticatedUser $authenticatedUser,
        protected QueryBusInterface $queryBus,
    ) {
    }

    protected function getEvent(string $uuid): EventView
    {
        try {
            $userUuid = $this->authenticatedUser->getUser()->getUuid();

            return $this->queryBus->handle(new GetEventByOwnerQuery($userUuid, $uuid));
        } catch (EventNotFoundException) {
            throw new NotFoundHttpException();
        }
    }
}
