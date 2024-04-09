<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Fixtures;

use App\Domain\Event\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class EventFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $event = new Event(
            'f1f992d3-3cf5-4eb2-9b83-f112b7234613',
            'Mariage H&M',
            new \DateTime('2019-01-05'),
            new \DateTime('2019-01-30'),
            $this->getReference('user'),
        );

        $manager->persist($event);
        $manager->flush();

        $this->addReference('event', $event);
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
        ];
    }
}
