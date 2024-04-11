<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\App\Event;

use App\Application\CommandBusInterface;
use App\Application\Event\Command\SaveEventCommand;
use App\Application\Event\Query\GetEventByOwnerQuery;
use App\Application\QueryBusInterface;
use App\Domain\Event\Exception\EventAlreadyExistException;
use App\Domain\Event\Exception\EventNotFoundException;
use App\Infrastructure\Form\Event\EventFormType;
use App\Infrastructure\Security\AuthenticatedUser;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class EditEventController
{
    public function __construct(
        private \Twig\Environment $twig,
        private CommandBusInterface $commandBus,
        private QueryBusInterface $queryBus,
        private AuthenticatedUser $authenticatedUser,
        private FormFactoryInterface $formFactory,
        private RouterInterface $router,
        private TranslatorInterface $translator,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    #[Route(
        '/events/{uuid}/edit',
        name: 'app_events_edit',
        requirements: ['uuid' => Requirement::UUID],
        methods: ['GET', 'POST'],
    )]
    public function __invoke(Request $request, string $uuid): Response
    {
        $ownerUuid = $this->authenticatedUser->getUser()->getUuid();

        try {
            $event = $this->queryBus->handle(new GetEventByOwnerQuery($ownerUuid, $uuid));
        } catch (EventNotFoundException) {
            throw new NotFoundHttpException();
        }

        $command = SaveEventCommand::createFromView($event, $ownerUuid);
        $form = $this->formFactory->create(EventFormType::class, $command, [
            'action' => $this->router->generate('app_events_edit', ['uuid' => $uuid]),
        ]);
        $form->handleRequest($request);
        $commandFailed = false;

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $uuid = $this->commandBus->handle($command);

                return new RedirectResponse($this->urlGenerator->generate('app_events_dashboard', ['uuid' => $uuid]));
            } catch (EventAlreadyExistException) {
                $commandFailed = true;
                $form->get('title')->addError(
                    new FormError($this->translator->trans('events.error.already_exist', [], 'validators')),
                );
            }
        }

        return new Response(
            content: $this->twig->render(
                name: 'app/event/edit.html.twig',
                context : [
                    'event' => $event,
                    'form' => $form->createView(),
                ],
            ),
            status: ($form->isSubmitted() && !$form->isValid()) || $commandFailed
                ? Response::HTTP_UNPROCESSABLE_ENTITY
                : Response::HTTP_OK,
        );
    }
}
