<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Fixtures;

use App\Domain\Tag\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class TagFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $tag1 = new Tag(
            '4d2bfad5-2e55-4059-ba43-783acb237772',
            'Cérémonie religieuse',
            new \DateTime('2019-01-05 19:00:00'),
            new \DateTime('2019-01-05 21:00:00'),
            $this->getReference('event'),
        );

        $tag2 = new Tag(
            '95af7a78-8a14-4f82-b41a-66ef01cbd603',
            'Dîner',
            new \DateTime('2019-01-05 21:01:00'),
            new \DateTime('2019-01-05 23:30:00'),
            $this->getReference('event'),
        );

        $tag3 = new Tag(
            '9abf583d-8128-4da1-a359-736cfd3d13db',
            'Bubble foot',
            new \DateTime('2023-05-05 08:00:00'),
            new \DateTime('2023-05-10 23:00:00'),
            $this->getReference('event2'),
        );

        $manager->persist($tag1);
        $manager->persist($tag2);
        $manager->persist($tag3);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            EventFixture::class,
        ];
    }
}
