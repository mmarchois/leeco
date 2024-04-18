<?php

declare(strict_types=1);

namespace App\Domain\User\Repository;

use App\Domain\User\User;

interface UserRepositoryInterface
{
    public function add(User $user): User;

    public function delete(User $user): void;

    public function findOneByEmail(string $email): ?User;

    public function findOneByUuid(string $uuid): ?User;
}
