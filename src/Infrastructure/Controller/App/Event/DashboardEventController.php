<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\App\Event;

use App\Application\Event\Query\GetEventByOwnerQuery;
use App\Application\QueryBusInterface;
use App\Domain\Event\Exception\EventNotFoundException;
use App\Infrastructure\Security\AuthenticatedUser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;

final class DashboardEventController
{
    public function __construct(
        private \Twig\Environment $twig,
        private QueryBusInterface $queryBus,
        private AuthenticatedUser $authenticatedUser,
    ) {
    }

    #[Route(
        '/events/{uuid}',
        name: 'app_events_dashboard',
        requirements: ['uuid' => Requirement::UUID],
        methods: ['GET'],
    )]
    public function __invoke(string $uuid): Response
    {
        $ownerUuid = $this->authenticatedUser->getUser()->getUuid();

        try {
            $event = $this->queryBus->handle(new GetEventByOwnerQuery($ownerUuid, $uuid));
        } catch (EventNotFoundException) {
            throw new NotFoundHttpException();
        }

        return new Response(
            content: $this->twig->render(
                name: 'app/event/dashboard.html.twig',
                context : [
                    'event' => $event,
                ],
            ),
        );
    }
}
