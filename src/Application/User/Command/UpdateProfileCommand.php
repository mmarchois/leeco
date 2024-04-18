<?php

declare(strict_types=1);

namespace App\Application\User\Command;

use App\Application\CommandInterface;
use App\Domain\User\User;

final class UpdateProfileCommand implements CommandInterface
{
    public ?string $firstName;
    public ?string $lastName;
    public ?string $email;

    public function __construct(
        public readonly User $user,
    ) {
        $this->firstName = $user->getFirstName();
        $this->lastName = $user->getLastName();
        $this->email = $user->getEmail();
    }
}
