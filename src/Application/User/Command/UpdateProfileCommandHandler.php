<?php

declare(strict_types=1);

namespace App\Application\User\Command;

use App\Domain\User\Exception\UserAlreadyRegisteredException;
use App\Domain\User\Specification\IsUserAlreadyRegistered;
use App\Domain\User\User;

final readonly class UpdateProfileCommandHandler
{
    public function __construct(
        private IsUserAlreadyRegistered $isUserAlreadyRegistered,
    ) {
    }

    public function __invoke(UpdateProfileCommand $command): User
    {
        $email = trim(strtolower($command->email));

        if ($email !== $command->user->getEmail() && $this->isUserAlreadyRegistered->isSatisfiedBy($email)) {
            throw new UserAlreadyRegisteredException();
        }

        $command->user->update(
            firstName: $command->firstName,
            lastName: $command->lastName,
            email: $email,
        );

        return $command->user;
    }
}
