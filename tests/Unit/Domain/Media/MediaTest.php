<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Media;

use App\Domain\Event\Event;
use App\Domain\Guest\Guest;
use App\Domain\Media\Media;
use App\Domain\Media\MediaOriginEnum;
use PHPUnit\Framework\TestCase;

final class MediaTest extends TestCase
{
    public function testGetters(): void
    {
        $event = $this->createMock(Event::class);
        $guest = $this->createMock(Guest::class);
        $createdAt = new \DateTime('2023-01-01');

        $media = new Media(
            uuid: '9cebe00d-04d8-48da-89b1-059f6b7bfe44',
            path: '/path/to/media.jpg',
            origin: MediaOriginEnum::CAMERA->value,
            createdAt: $createdAt,
            event: $event,
            guest: $guest,
        );

        $this->assertSame('9cebe00d-04d8-48da-89b1-059f6b7bfe44', $media->getUuid());
        $this->assertSame('/path/to/media.jpg', $media->getPath());
        $this->assertSame(MediaOriginEnum::CAMERA->value, $media->getOrigin());
        $this->assertSame($event, $media->getEvent());
        $this->assertSame($createdAt, $media->getCreatedAt());
        $this->assertSame($guest, $media->getGuest());

        $media->updatePath('/new/path/media.png');
        $this->assertSame('/new/path/media.png', $media->getPath());
    }
}
