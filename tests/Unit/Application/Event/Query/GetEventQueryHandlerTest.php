<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Event\Query;

use App\Application\Event\Query\GetEventQuery;
use App\Application\Event\Query\GetEventQueryHandler;
use App\Domain\Event\Event;
use App\Domain\Event\Exception\EventNotFoundException;
use App\Domain\Event\Exception\EventNotOwnedByUserException;
use App\Domain\Event\Repository\EventRepositoryInterface;
use App\Domain\Event\Specification\IsEventOwnedByUser;
use PHPUnit\Framework\TestCase;

final class GetEventQueryHandlerTest extends TestCase
{
    public function testGetEvent(): void
    {
        $eventRepository = $this->createMock(EventRepositoryInterface::class);
        $isEventOwnedByUser = $this->createMock(IsEventOwnedByUser::class);
        $event = $this->createMock(Event::class);

        $eventRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->with('bdf7aec7-232b-4b01-b7ae-d05b701a2502')
            ->willReturn($event);

        $isEventOwnedByUser
            ->expects(self::once())
            ->method('isSatisfiedBy')
            ->with($event, '33795ae7-395a-440a-9fa8-f72343d62eb0')
            ->willReturn(true);

        $query = new GetEventQuery('33795ae7-395a-440a-9fa8-f72343d62eb0', 'bdf7aec7-232b-4b01-b7ae-d05b701a2502');
        $handler = new GetEventQueryHandler($eventRepository, $isEventOwnedByUser);

        $this->assertEquals($event, ($handler)($query));
    }

    public function testUserCannotAccess(): void
    {
        $this->expectException(EventNotOwnedByUserException::class);

        $event = $this->createMock(Event::class);
        $isEventOwnedByUser = $this->createMock(IsEventOwnedByUser::class);
        $eventRepository = $this->createMock(EventRepositoryInterface::class);
        $eventRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->with('bdf7aec7-232b-4b01-b7ae-d05b701a2502')
            ->willReturn($event);

        $isEventOwnedByUser
            ->expects(self::once())
            ->method('isSatisfiedBy')
            ->with($event, '33795ae7-395a-440a-9fa8-f72343d62eb0')
            ->willReturn(false);

        $query = new GetEventQuery('33795ae7-395a-440a-9fa8-f72343d62eb0', 'bdf7aec7-232b-4b01-b7ae-d05b701a2502');
        $handler = new GetEventQueryHandler($eventRepository, $isEventOwnedByUser);

        ($handler)($query);
    }

    public function testEventNotFound(): void
    {
        $this->expectException(EventNotFoundException::class);

        $isEventOwnedByUser = $this->createMock(IsEventOwnedByUser::class);
        $eventRepository = $this->createMock(EventRepositoryInterface::class);
        $eventRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->with('bdf7aec7-232b-4b01-b7ae-d05b701a2502')
            ->willReturn(null);

        $isEventOwnedByUser
            ->expects(self::never())
            ->method('isSatisfiedBy');

        $query = new GetEventQuery('33795ae7-395a-440a-9fa8-f72343d62eb0', 'bdf7aec7-232b-4b01-b7ae-d05b701a2502');
        $handler = new GetEventQueryHandler($eventRepository, $isEventOwnedByUser);

        ($handler)($query);
    }
}
