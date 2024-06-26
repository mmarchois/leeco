<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\App\Event;

use App\Application\Event\Query\GetEventsQuery;
use App\Application\QueryBusInterface;
use App\Infrastructure\Security\AuthenticatedUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class ListEventsController
{
    public function __construct(
        private \Twig\Environment $twig,
        private QueryBusInterface $queryBus,
        private AuthenticatedUser $authenticatedUser,
    ) {
    }

    #[Route(
        '/events/{page}',
        name: 'app_events_list',
        requirements: ['page' => '\d+'],
        methods: ['GET'],
    )]
    public function __invoke(Request $request, int $page = 1): Response
    {
        $pageSize = min($request->query->getInt('pageSize', 20), 50);
        if (0 === $pageSize) {
            throw new BadRequestHttpException();
        }

        $userUuid = $this->authenticatedUser->getUser()->getUuid();
        $paginatedEvents = $this->queryBus->handle(new GetEventsQuery($userUuid, $page, $pageSize));

        return new Response(
            content: $this->twig->render(
                name: 'app/event/list.html.twig',
                context : [
                    'paginatedEvents' => $paginatedEvents,
                ],
            ),
        );
    }
}
