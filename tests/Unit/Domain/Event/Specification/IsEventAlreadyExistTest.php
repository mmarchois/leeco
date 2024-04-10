<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Event\Specification;

use App\Domain\Event\Event;
use App\Domain\Event\Repository\EventRepositoryInterface;
use App\Domain\Event\Specification\IsEventAlreadyExist;
use PHPUnit\Framework\TestCase;

final class IsEventAlreadyExistTest extends TestCase
{
    public function testEventExist(): void
    {
        $event = $this->createMock(Event::class);
        $eventRepository = $this->createMock(EventRepositoryInterface::class);
        $eventRepository
            ->expects(self::once())
            ->method('findEventByTitleAndOwner')
            ->with('Mariage H&M', '6202205c-d16f-4dc8-b8da-b3cd31539a08')
            ->willReturn($event);

        $pattern = new IsEventAlreadyExist($eventRepository);
        $this->assertTrue($pattern->isSatisfiedBy('6202205c-d16f-4dc8-b8da-b3cd31539a08', 'Mariage H&M'));
    }

    public function testEventDoesntExist(): void
    {
        $eventRepository = $this->createMock(EventRepositoryInterface::class);
        $eventRepository
            ->expects(self::once())
            ->method('findEventByTitleAndOwner')
            ->with('Mariage H&M', '6202205c-d16f-4dc8-b8da-b3cd31539a08')
            ->willReturn(null);

        $pattern = new IsEventAlreadyExist($eventRepository);
        $this->assertFalse($pattern->isSatisfiedBy('6202205c-d16f-4dc8-b8da-b3cd31539a08', 'Mariage H&M'));
    }
}
