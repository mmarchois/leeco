<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Fixtures;

use App\Domain\Media\Media;
use App\Domain\Media\MediaOriginEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class MediaFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $eventBanner = new Media(
            uuid: '018fa9ec-a7ef-716c-bfbd-9a0f01cdf1dd',
            path: 'f1f992d3-3cf5-4eb2-9b83-f112b7234613/018fa9ec-a7ef-716c-bfbd-9a0f01cdf1dd.jpg',
            origin: MediaOriginEnum::CAMERA->value,
            createdAt: new \DateTime('2024-05-29'),
            event: $this->getReference('event'),
        );

        $guestMedia = new Media(
            uuid: '7d5cc99f-768e-4ba9-8dff-54eb0389b923',
            path: 'f1f992d3-3cf5-4eb2-9b83-f112b7234613/7d5cc99f-768e-4ba9-8dff-54eb0389b923.jpg',
            origin: MediaOriginEnum::CAMERA->value,
            createdAt: new \DateTime('2024-05-30'),
            event: $this->getReference('event'),
            guest: $this->getReference('guest1'),
        );

        $manager->persist($eventBanner);
        $manager->persist($guestMedia);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            EventFixture::class,
            GuestFixture::class,
        ];
    }
}
