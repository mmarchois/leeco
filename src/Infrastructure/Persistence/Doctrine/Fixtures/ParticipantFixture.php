<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Fixtures;

use App\Domain\Participant\Participant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class ParticipantFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $participant1 = new Participant(
            '0faf6d38-6887-44b9-9896-7877e31c56c4',
            'Tony & Corinne',
            'MARCHOIS',
            'tc.marchois@gmail.com',
            'accessCode1',
            new \DateTime('2023-01-01'),
            $this->getReference('event'),
            false,
        );

        $participant2 = new Participant(
            '6f6973d5-6733-415e-bd35-432a6b50f8cf',
            'Floran',
            'ROISIN',
            'floran.roisin@gmail.com',
            'accessCode2',
            new \DateTime('2023-02-02'),
            $this->getReference('event'),
            true,
        );

        $participant3 = new Participant(
            'e4095f02-1516-42b3-82d1-506f2e74f027',
            'Anais',
            'MARCHOIS',
            'anais.marchois@gmail.com',
            'accessCode3',
            new \DateTime('2023-03-03'),
            $this->getReference('event2'),
            false,
        );

        $manager->persist($participant1);
        $manager->persist($participant2);
        $manager->persist($participant3);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            EventFixture::class,
        ];
    }
}
