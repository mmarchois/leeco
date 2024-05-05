<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Application\CommandBusInterface;
use App\Application\User\Command\ResetPasswordCommand;
use App\Domain\User\Exception\TokenExpiredException;
use App\Domain\User\Exception\TokenNotFoundException;
use App\Infrastructure\Form\User\ResetPasswordFormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final readonly class ResetPasswordController
{
    public function __construct(
        private \Twig\Environment $twig,
        private CommandBusInterface $commandBus,
        private UrlGeneratorInterface $urlGenerator,
        private FormFactoryInterface $formFactory,
    ) {
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password', methods: ['GET', 'POST'])]
    public function __invoke(Request $request, string $token): Response
    {
        $command = new ResetPasswordCommand($token);
        $form = $this->formFactory->create(ResetPasswordFormType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->commandBus->handle($command);

                return new RedirectResponse($this->urlGenerator->generate('app_login', ['reset' => 1]));
            } catch (TokenNotFoundException|TokenExpiredException) {
                return new RedirectResponse($this->urlGenerator->generate('app_reset_password', [
                    'token' => $token,
                    'error' => 1,
                ]));
            }
        }

        return new Response(
            content: $this->twig->render(
                name: 'reset-password.html.twig',
                context: [
                    'form' => $form->createView(),
                ],
            ),
            status: ($form->isSubmitted() && !$form->isValid())
                ? Response::HTTP_UNPROCESSABLE_ENTITY
                : Response::HTTP_OK,
        );
    }
}
