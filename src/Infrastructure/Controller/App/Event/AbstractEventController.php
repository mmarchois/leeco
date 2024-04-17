<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\App\Event;

use App\Application\Event\Query\GetEventQuery;
use App\Application\QueryBusInterface;
use App\Domain\Event\Event;
use App\Domain\Event\Exception\EventNotFoundException;
use App\Domain\Event\Exception\EventNotOwnedByUserException;
use App\Infrastructure\Security\AuthenticatedUser;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class AbstractEventController
{
    public function __construct(
        protected AuthenticatedUser $authenticatedUser,
        protected QueryBusInterface $queryBus,
    ) {
    }

    protected function getEvent(string $uuid): Event
    {
        try {
            $userUuid = $this->authenticatedUser->getUser()->getUuid();

            return $this->queryBus->handle(new GetEventQuery($userUuid, $uuid));
        } catch (EventNotFoundException) {
            throw new NotFoundHttpException();
        } catch (EventNotOwnedByUserException) {
            throw new AccessDeniedHttpException();
        }
    }
}
