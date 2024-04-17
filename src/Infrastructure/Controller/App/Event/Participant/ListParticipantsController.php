<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\App\Event\Participant;

use App\Application\Participant\Query\GetParticipantsByEventQuery;
use App\Application\QueryBusInterface;
use App\Infrastructure\Controller\App\Event\AbstractEventController;
use App\Infrastructure\Security\AuthenticatedUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;

final class ListParticipantsController extends AbstractEventController
{
    public function __construct(
        private \Twig\Environment $twig,
        QueryBusInterface $queryBus,
        AuthenticatedUser $authenticatedUser,
    ) {
        parent::__construct($authenticatedUser, $queryBus);
    }

    #[Route(
        '/events/{uuid}/participants/{page}',
        name: 'app_participants_list',
        requirements: ['page' => '\d+', 'uuid' => Requirement::UUID],
        methods: ['GET'],
    )]
    public function __invoke(Request $request, string $uuid, int $page = 1): Response
    {
        $pageSize = min($request->query->getInt('pageSize', 20), 50);
        if (0 === $pageSize) {
            throw new BadRequestHttpException();
        }

        $event = $this->getEvent($uuid);
        $paginatedParticipants = $this->queryBus->handle(
            new GetParticipantsByEventQuery($event->getUuid(), $page, $pageSize),
        );

        return new Response(
            content: $this->twig->render(
                name: 'app/participant/list.html.twig',
                context : [
                    'paginatedParticipants' => $paginatedParticipants,
                    'event' => $event,
                ],
            ),
        );
    }
}
