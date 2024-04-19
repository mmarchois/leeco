<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\App\Event\Tag;

use App\Application\CommandBusInterface;
use App\Application\QueryBusInterface;
use App\Application\Tag\Command\SaveTagCommand;
use App\Application\Tag\Query\GetTagQuery;
use App\Domain\Tag\Exception\TagAlreadyExistException;
use App\Domain\Tag\Exception\TagNotBelongsToEventException;
use App\Domain\Tag\Exception\TagNotFoundException;
use App\Infrastructure\Controller\App\Event\AbstractEventController;
use App\Infrastructure\Form\Tag\TagFormType;
use App\Infrastructure\Security\AuthenticatedUser;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class EditTagController extends AbstractEventController
{
    public function __construct(
        private \Twig\Environment $twig,
        private CommandBusInterface $commandBus,
        private FormFactoryInterface $formFactory,
        private RouterInterface $router,
        private UrlGeneratorInterface $urlGenerator,
        private TranslatorInterface $translator,
        AuthenticatedUser $authenticatedUser,
        QueryBusInterface $queryBus,
    ) {
        parent::__construct($authenticatedUser, $queryBus);
    }

    #[Route(
        '/events/{eventUuid}/tags/{uuid}/edit',
        name: 'app_tags_edit',
        requirements: ['eventUuid' => Requirement::UUID, 'uuid' => Requirement::UUID],
        methods: ['GET', 'POST'],
    )]
    public function __invoke(Request $request, string $eventUuid, string $uuid): Response
    {
        $event = $this->getEvent($eventUuid);

        try {
            $tag = $this->queryBus->handle(new GetTagQuery($event, $uuid));
        } catch (TagNotFoundException) {
            throw new NotFoundHttpException();
        } catch (TagNotBelongsToEventException) {
            throw new AccessDeniedHttpException();
        }

        $command = SaveTagCommand::create($event, $tag);
        $form = $this->formFactory->create(TagFormType::class, $command, [
            'action' => $this->router->generate('app_tags_edit', ['eventUuid' => $eventUuid, 'uuid' => $uuid]),
        ]);
        $form->handleRequest($request);
        $commandFailed = false;

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->commandBus->handle($command);

                return new RedirectResponse($this->urlGenerator->generate('app_tags_list', ['uuid' => $eventUuid]));
            } catch (TagAlreadyExistException) {
                $commandFailed = true;
                $form->get('title')->addError(
                    new FormError($this->translator->trans('tag.error.already_exist', [], 'validators')),
                );
            }
        }

        return new Response(
            content: $this->twig->render(
                name: 'app/tag/edit.html.twig',
                context : [
                    'form' => $form->createView(),
                    'tag' => $tag,
                ],
            ),
            status: ($form->isSubmitted() && !$form->isValid()) || $commandFailed
                ? Response::HTTP_UNPROCESSABLE_ENTITY
                : Response::HTTP_OK,
        );
    }
}
