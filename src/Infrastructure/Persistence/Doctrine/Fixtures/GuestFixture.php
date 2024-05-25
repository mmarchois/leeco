<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Fixtures;

use App\Domain\Guest\Guest;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class GuestFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $guest1 = new Guest(
            '0faf6d38-6887-44b9-9896-7877e31c56c4',
            'Tony',
            'MARCHOIS',
            '9C287922-EE26-4501-94B5-DDE6F83E1475',
            new \DateTime('2023-01-01'),
            $this->getReference('event'),
        );

        $guest2 = new Guest(
            '6f6973d5-6733-415e-bd35-432a6b50f8cf',
            'Corinne',
            'Marchois',
            '9774d56d682e549c',
            new \DateTime('2023-02-02'),
            $this->getReference('event'),
        );

        $guest3 = new Guest(
            'e4095f02-1516-42b3-82d1-506f2e74f027',
            'Julien',
            'MARCHOIS',
            '99A4D301-53F5-11CB-8CA0-9CA39A9E1F01',
            new \DateTime('2023-03-03'),
            $this->getReference('event2'),
        );

        $manager->persist($guest1);
        $manager->persist($guest2);
        $manager->persist($guest3);
        $manager->flush();

        $this->addReference('guest1', $guest1);
        $this->addReference('guest2', $guest2);
        $this->addReference('guest3', $guest3);
    }

    public function getDependencies(): array
    {
        return [
            EventFixture::class,
        ];
    }
}
