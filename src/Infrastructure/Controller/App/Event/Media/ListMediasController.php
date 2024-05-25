<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\App\Event\Media;

use App\Application\Media\Query\GetMediasByEventQuery;
use App\Application\QueryBusInterface;
use App\Infrastructure\Controller\App\Event\AbstractEventController;
use App\Infrastructure\Security\AuthenticatedUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;

final class ListMediasController extends AbstractEventController
{
    public function __construct(
        private \Twig\Environment $twig,
        QueryBusInterface $queryBus,
        AuthenticatedUser $authenticatedUser,
    ) {
        parent::__construct($authenticatedUser, $queryBus);
    }

    #[Route(
        '/events/{eventUuid}/medias/{page}',
        name: 'app_medias_list',
        requirements: ['page' => '\d+', 'eventUuid' => Requirement::UUID],
        methods: ['GET'],
    )]
    public function __invoke(Request $request, string $eventUuid, int $page = 1): Response
    {
        $pageSize = min($request->query->getInt('pageSize', 20), 50);
        if (0 === $pageSize) {
            throw new BadRequestHttpException();
        }

        $event = $this->getEvent($eventUuid);
        $paginatedMedias = $this->queryBus->handle(
            new GetMediasByEventQuery($event->getUuid(), $page, $pageSize),
        );

        return new Response(
            content: $this->twig->render(
                name: 'app/media/list.html.twig',
                context : [
                    'paginatedMedias' => $paginatedMedias,
                    'event' => $event,
                ],
            ),
        );
    }
}
