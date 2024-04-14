<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\App\Event\Participant;

use App\Application\CommandBusInterface;
use App\Application\Participant\Command\SaveParticipantCommand;
use App\Application\QueryBusInterface;
use App\Domain\Participant\Exception\ParticipantAlreadyExistException;
use App\Infrastructure\Controller\App\Event\AbstractEventController;
use App\Infrastructure\Form\Participant\ParticipantFormType;
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

final class AddParticipantController extends AbstractEventController
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
        '/events/{uuid}/participants/add',
        name: 'app_participants_add',
        methods: ['GET', 'POST'],
    )]
    public function __invoke(Request $request, string $uuid): Response
    {
        $event = $this->getEvent($uuid);
        $command = new SaveParticipantCommand($uuid);
        $form = $this->formFactory->create(ParticipantFormType::class, $command, [
            'action' => $this->router->generate('app_participants_add', ['uuid' => $uuid]),
        ]);
        $form->handleRequest($request);
        $commandFailed = false;

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->commandBus->handle($command);

                return new RedirectResponse($this->urlGenerator->generate('app_participants_list', ['uuid' => $uuid]));
            } catch (ParticipantAlreadyExistException) {
                $commandFailed = true;
                $form->get('email')->addError(
                    new FormError($this->translator->trans('participant.error.already_exist', [], 'validators')),
                );
            }
        }

        return new Response(
            content: $this->twig->render(
                name: 'app/participant/add.html.twig',
                context : [
                    'form' => $form->createView(),
                    'event' => $event,
                ],
            ),
            status: ($form->isSubmitted() && !$form->isValid()) || $commandFailed
                ? Response::HTTP_UNPROCESSABLE_ENTITY
                : Response::HTTP_OK,
        );
    }
}
