<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Media;

use App\Domain\Event\Event;
use App\Domain\Media\Media;
use App\Domain\Media\MediaTypeEnum;
use PHPUnit\Framework\TestCase;

final class MediaTest extends TestCase
{
    public function testGetters(): void
    {
        $event = $this->createMock(Event::class);
        $createdAt = new \DateTime('2023-01-01');

        $media = new Media(
            '9cebe00d-04d8-48da-89b1-059f6b7bfe44',
            '/path/to/media.jpg',
            MediaTypeEnum::EVENT_BANNER->value,
            $createdAt,
            $event,
        );

        $this->assertSame('9cebe00d-04d8-48da-89b1-059f6b7bfe44', $media->getUuid());
        $this->assertSame('/path/to/media.jpg', $media->getPath());
        $this->assertSame(MediaTypeEnum::EVENT_BANNER->value, $media->getType());
        $this->assertSame($event, $media->getEvent());
        $this->assertSame($createdAt, $media->getCreatedAt());

        $media->updatePath('/new/path/media.png');
        $this->assertSame('/new/path/media.png', $media->getPath());
    }
}
