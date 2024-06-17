<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Api\Guest;

use App\Application\CommandBusInterface;
use App\Application\Guest\Command\SaveGuestCommand;
use App\Domain\Event\Exception\EventNotFoundException;
use App\Domain\Guest\Exception\GuestAlreadyExistException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class RegisterController
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private TranslatorInterface $translator,
    ) {
    }

    #[Route(
        '/guests/register',
        name: 'api_register_guest',
        methods: ['POST'],
    )]
    public function __invoke(
        #[MapRequestPayload] SaveGuestCommand $command,
    ): JsonResponse {
        try {
            $guestUuid = $this->commandBus->handle($command);

            return new JsonResponse(data: ['uuid' => $guestUuid], status: Response::HTTP_CREATED);
        } catch (EventNotFoundException) {
            throw new BadRequestHttpException($this->translator->trans('event.error.not_found', [], 'validators'));
        } catch (GuestAlreadyExistException) {
            throw new BadRequestHttpException($this->translator->trans('guest.error.already_exist', [], 'validators'));
        }
    }
}
