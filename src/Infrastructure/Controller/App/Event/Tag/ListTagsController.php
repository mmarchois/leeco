<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\App\Event\Tag;

use App\Application\QueryBusInterface;
use App\Application\Tag\Query\GetTagsByEventQuery;
use App\Infrastructure\Controller\App\Event\AbstractEventController;
use App\Infrastructure\Security\AuthenticatedUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;

final class ListTagsController extends AbstractEventController
{
    public function __construct(
        private \Twig\Environment $twig,
        QueryBusInterface $queryBus,
        AuthenticatedUser $authenticatedUser,
    ) {
        parent::__construct($authenticatedUser, $queryBus);
    }

    #[Route(
        '/events/{eventUuid}/tags/{page}',
        name: 'app_tags_list',
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
        $paginatedTags = $this->queryBus->handle(
            new GetTagsByEventQuery($event->getUuid(), $page, $pageSize),
        );

        return new Response(
            content: $this->twig->render(
                name: 'app/tag/list.html.twig',
                context : [
                    'paginatedTags' => $paginatedTags,
                    'event' => $event,
                ],
            ),
        );
    }
}
