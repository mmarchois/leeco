<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Fixtures;

use App\Domain\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class UserFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User(
            '0b507871-8b5e-4575-b297-a630310fc06e',
            'Mathieu',
            'MARCHOIS',
            'mathieu.marchois@gmail.com',
            'password123',
            true,
        );

        $userNotVerified = new User(
            'cbb1f1ac-8343-474c-8e8a-b7c98384da9a',
            'Hélène',
            'MARCHOIS',
            'helene.m.maitre@gmail.com',
            'password123',
            false,
        );

        $manager->persist($user);
        $manager->persist($userNotVerified);
        $manager->flush();

        $this->addReference('user', $user);
        $this->addReference('userNotVerified', $userNotVerified);
    }
}
