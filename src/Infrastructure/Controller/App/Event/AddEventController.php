<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\App\Event;

use App\Application\CommandBusInterface;
use App\Application\Event\Command\SaveEventCommand;
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
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AddEventController
{
    public function __construct(
        private \Twig\Environment $twig,
        private CommandBusInterface $commandBus,
        private AuthenticatedUser $authenticatedUser,
        private FormFactoryInterface $formFactory,
        private RouterInterface $router,
        private TranslatorInterface $translator,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    #[Route(
        '/events/add',
        name: 'app_events_add',
        methods: ['GET', 'POST'],
    )]
    public function __invoke(Request $request): Response
    {
        $userUuid = $this->authenticatedUser->getUser()->getUuid();
        $command = new SaveEventCommand($userUuid);
        $form = $this->formFactory->create(EventFormType::class, $command, [
            'action' => $this->router->generate('app_events_add'),
        ]);
        $form->handleRequest($request);
        $commandFailed = false;

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $uuid = $this->commandBus->handle($command);

                return new RedirectResponse($this->urlGenerator->generate('app_events_list'));
            } catch (EventAlreadyExistException) {
                $commandFailed = true;
                $form->get('title')->addError(
                    new FormError($this->translator->trans('event.error.already_exist', [], 'validators')),
                );
            }
        }

        return new Response(
            content: $this->twig->render(
                name: 'app/event/add.html.twig',
                context : [
                    'form' => $form->createView(),
                ],
            ),
            status: ($form->isSubmitted() && !$form->isValid()) || $commandFailed
                ? Response::HTTP_UNPROCESSABLE_ENTITY
                : Response::HTTP_OK,
        );
    }
}
