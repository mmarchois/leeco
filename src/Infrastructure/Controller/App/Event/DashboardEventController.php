<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\App\Event;

use App\Application\QueryBusInterface;
use App\Infrastructure\Security\AuthenticatedUser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;

final class DashboardEventController extends AbstractEventController
{
    public function __construct(
        private \Twig\Environment $twig,
        QueryBusInterface $queryBus,
        AuthenticatedUser $authenticatedUser,
    ) {
        parent::__construct($authenticatedUser, $queryBus);
    }

    #[Route(
        '/events/{uuid}',
        name: 'app_events_dashboard',
        requirements: ['uuid' => Requirement::UUID],
        methods: ['GET'],
    )]
    public function __invoke(string $uuid): Response
    {
        $event = $this->getEvent($uuid);

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
