<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Fragments\Event\Tag;

use App\Application\CommandBusInterface;
use App\Application\QueryBusInterface;
use App\Application\Tag\Command\DeleteTagCommand;
use App\Domain\Tag\Exception\TagNotBelongsToEventException;
use App\Domain\Tag\Exception\TagNotFoundException;
use App\Infrastructure\Controller\App\Event\AbstractEventController;
use App\Infrastructure\Security\AuthenticatedUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\UX\Turbo\TurboBundle;

final class DeleteTagFragmentController extends AbstractEventController
{
    public function __construct(
        private \Twig\Environment $twig,
        private CommandBusInterface $commandBus,
        private CsrfTokenManagerInterface $csrfTokenManager,
        AuthenticatedUser $authenticatedUser,
        QueryBusInterface $queryBus,
    ) {
        parent::__construct($authenticatedUser, $queryBus);
    }

    #[Route(
        '/events/{eventUuid}/tags/{uuid}/delete',
        name: 'fragment_tag_delete',
        requirements: ['eventUuid' => Requirement::UUID, 'uuid' => Requirement::UUID],
        methods: ['DELETE'],
    )]
    public function __invoke(Request $request, string $eventUuid, string $uuid): Response
    {
        $csrfToken = new CsrfToken('delete-tag', $request->request->get('token'));
        if (!$this->csrfTokenManager->isTokenValid($csrfToken)) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $event = $this->getEvent($eventUuid);

        try {
            $this->commandBus->handle(new DeleteTagCommand($event, $uuid));
        } catch (TagNotFoundException) {
            throw new NotFoundHttpException();
        } catch (TagNotBelongsToEventException) {
            throw new AccessDeniedHttpException();
        }

        $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

        return new Response(
            $this->twig->render(
                name: 'fragments/tag/deleted.stream.html.twig',
                context: [
                    'uuid' => $uuid,
                ],
            ),
        );
    }
}
