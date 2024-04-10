<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Event\Query;

use App\Application\Event\Query\GetEventByOwnerQuery;
use App\Application\Event\Query\GetEventByOwnerQueryHandler;
use App\Application\Event\View\EventView;
use App\Domain\Event\Exception\EventNotFoundException;
use App\Domain\Event\Repository\EventRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class GetEventByOwnerQueryHandlerTest extends TestCase
{
    public function testGetEvent(): void
    {
        $eventRepository = $this->createMock(EventRepositoryInterface::class);
        $expectedResult = new EventView('33795ae7-395a-440a-9fa8-f72343d62eb0', 'Mariage H&M');

        $eventRepository
            ->expects(self::once())
            ->method('findOneByUuidAndOwner')
            ->with('33795ae7-395a-440a-9fa8-f72343d62eb0', 'bdf7aec7-232b-4b01-b7ae-d05b701a2502')
            ->willReturn($expectedResult);

        $query = new GetEventByOwnerQuery('bdf7aec7-232b-4b01-b7ae-d05b701a2502', '33795ae7-395a-440a-9fa8-f72343d62eb0');
        $handler = new GetEventByOwnerQueryHandler($eventRepository);

        $this->assertEquals($expectedResult, ($handler)($query));
    }

    public function testEventNotFound(): void
    {
        $this->expectException(EventNotFoundException::class);

        $eventRepository = $this->createMock(EventRepositoryInterface::class);
        $eventRepository
            ->expects(self::once())
            ->method('findOneByUuidAndOwner')
            ->with('33795ae7-395a-440a-9fa8-f72343d62eb0', 'bdf7aec7-232b-4b01-b7ae-d05b701a2502')
            ->willReturn(null);

        $query = new GetEventByOwnerQuery('bdf7aec7-232b-4b01-b7ae-d05b701a2502', '33795ae7-395a-440a-9fa8-f72343d62eb0');
        $handler = new GetEventByOwnerQueryHandler($eventRepository);

        ($handler)($query);
    }
}
