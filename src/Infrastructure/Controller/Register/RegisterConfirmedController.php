<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Register;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RegisterConfirmedController
{
    public function __construct(
        private readonly \Twig\Environment $twig,
    ) {
    }

    #[Route('/register/confirmed', name: 'app_register_confirmed', methods: ['GET'])]
    public function __invoke(): Response
    {
        return new Response($this->twig->render('register/confirmed.html.twig'));
    }
}
