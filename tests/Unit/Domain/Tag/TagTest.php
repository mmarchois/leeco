<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Tag;

use App\Domain\Event\Event;
use App\Domain\Tag\Tag;
use PHPUnit\Framework\TestCase;

final class TagTest extends TestCase
{
    public function testGetters(): void
    {
        $startDate = new \DateTime('2023-11-19 18:00:00');
        $endDate = new \DateTime('2023-11-19 20:00:00');
        $event = $this->createMock(Event::class);

        $tag = new Tag(
            '9cebe00d-04d8-48da-89b1-059f6b7bfe44',
            'Cérémonie religieuse',
            $startDate,
            $endDate,
            $event,
        );

        $this->assertSame('9cebe00d-04d8-48da-89b1-059f6b7bfe44', $tag->getUuid());
        $this->assertSame('Cérémonie religieuse', $tag->getTitle());
        $this->assertSame($startDate, $tag->getStartDate());
        $this->assertSame($endDate, $tag->getEndDate());
        $this->assertSame($event, $tag->getEvent());

        $startDate2 = new \DateTime('2023-11-19 20:00:00');
        $endDate2 = new \DateTime('2023-11-19 22:00:00');
        $tag->update('Dîner', $startDate2, $endDate2);

        $this->assertSame('Dîner', $tag->getTitle());
        $this->assertSame($startDate2, $tag->getStartDate());
        $this->assertSame($endDate2, $tag->getEndDate());
    }
}
