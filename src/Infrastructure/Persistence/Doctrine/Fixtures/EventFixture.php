<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Fixtures;

use App\Domain\Event\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class EventFixture extends Fixture implements DependentFixtureInterface
{
    private \DateTimeZone $clientTimezone;

    public function __construct(
        string $clientTimezone,
    ) {
        $this->clientTimezone = new \DateTimeZone($clientTimezone);
    }

    public function load(ObjectManager $manager): void
    {
        $event = new Event(
            'f1f992d3-3cf5-4eb2-9b83-f112b7234613',
            'Mariage H&M',
            'FR123456789',
            new \DateTime('2019-01-05', $this->clientTimezone),
            new \DateTime('2019-01-07', $this->clientTimezone),
            $this->getReference('user'),
        );

        $event2 = new Event(
            '2203014c-5d51-4e20-b607-2b48ffb3f0c7',
            'EVG Julien',
            'FR76556789',
            new \DateTime('2023-05-05', $this->clientTimezone),
            new \DateTime('2023-05-05', $this->clientTimezone),
            $this->getReference('user'),
        );

        $manager->persist($event);
        $manager->persist($event2);
        $manager->flush();

        $this->addReference('event', $event);
        $this->addReference('event2', $event2);
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
        ];
    }
}
