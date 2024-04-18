<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\App\Event;

use App\Application\CommandBusInterface;
use App\Application\Event\Command\DeleteEventCommand;
use App\Domain\Event\Exception\EventNotFoundException;
use App\Domain\Event\Exception\EventNotOwnedByUserException;
use App\Infrastructure\Security\AuthenticatedUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class DeleteEventController
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private CsrfTokenManagerInterface $csrfTokenManager,
        private AuthenticatedUser $authenticatedUser,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    #[Route(
        '/events/{uuid}/delete',
        name: 'app_events_delete',
        requirements: ['uuid' => Requirement::UUID],
        methods: ['DELETE'],
    )]
    public function __invoke(Request $request, string $uuid): RedirectResponse
    {
        $csrfToken = new CsrfToken('delete-event', $request->request->get('token'));
        if (!$this->csrfTokenManager->isTokenValid($csrfToken)) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        try {
            $userUuid = $this->authenticatedUser->getUser()->getUuid();
            $this->commandBus->handle(new DeleteEventCommand($uuid, $userUuid));
        } catch (EventNotFoundException) {
            throw new NotFoundHttpException();
        } catch (EventNotOwnedByUserException) {
            throw new AccessDeniedHttpException();
        }

        return new RedirectResponse($this->urlGenerator->generate('app_events_list'));
    }
}
