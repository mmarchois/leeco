<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\App\Event;

use App\Application\CommandBusInterface;
use App\Application\Event\Command\SaveEventCommand;
use App\Application\QueryBusInterface;
use App\Domain\Event\Exception\EventAlreadyExistException;
use App\Infrastructure\Form\Event\EventFormType;
use App\Infrastructure\Security\AuthenticatedUser;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class EditEventController extends AbstractEventController
{
    public function __construct(
        private \Twig\Environment $twig,
        private CommandBusInterface $commandBus,
        private FormFactoryInterface $formFactory,
        private RouterInterface $router,
        private TranslatorInterface $translator,
        private UrlGeneratorInterface $urlGenerator,
        AuthenticatedUser $authenticatedUser,
        QueryBusInterface $queryBus,
    ) {
        parent::__construct($authenticatedUser, $queryBus);
    }

    #[Route(
        '/events/{eventUuid}/edit',
        name: 'app_events_edit',
        requirements: ['eventUuid' => Requirement::UUID],
        methods: ['GET', 'POST'],
    )]
    public function __invoke(Request $request, string $eventUuid): Response
    {
        $event = $this->getEvent($eventUuid);
        $command = SaveEventCommand::create($event);
        $form = $this->formFactory->create(EventFormType::class, $command, [
            'action' => $this->router->generate('app_events_edit', ['eventUuid' => $eventUuid]),
        ]);
        $form->handleRequest($request);
        $commandFailed = false;

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $uuid = $this->commandBus->handle($command);

                return new RedirectResponse($this->urlGenerator->generate('app_events_dashboard', ['eventUuid' => $uuid]));
            } catch (EventAlreadyExistException) {
                $commandFailed = true;
                $form->get('title')->addError(
                    new FormError($this->translator->trans('event.error.already_exist', [], 'validators')),
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
