<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Event\Command;

use App\Application\Event\Command\DeleteEventCommand;
use App\Application\Event\Command\DeleteEventCommandHandler;
use App\Domain\Event\Event;
use App\Domain\Event\Exception\EventNotFoundException;
use App\Domain\Event\Exception\EventNotOwnedByUserException;
use App\Domain\Event\Repository\EventRepositoryInterface;
use App\Domain\Event\Specification\IsEventOwnedByUser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DeleteEventCommandHandlerTest extends TestCase
{
    private MockObject $eventRepository;
    private MockObject $isEventOwnedByUser;
    private MockObject $event;
    private DeleteEventCommand $command;

    public function setUp(): void
    {
        $this->eventRepository = $this->createMock(EventRepositoryInterface::class);
        $this->isEventOwnedByUser = $this->createMock(IsEventOwnedByUser::class);
        $this->event = $this->createMock(Event::class);

        $command = new DeleteEventCommand('f9c69d5b-6e68-44f0-86f2-a7e93136410c', '5555e6a0-94eb-480b-9b66-5b0041011d12');
        $this->command = $command;
    }

    public function testDelete(): void
    {
        $this->eventRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->with('f9c69d5b-6e68-44f0-86f2-a7e93136410c')
            ->willReturn($this->event);

        $this->isEventOwnedByUser
            ->expects(self::once())
            ->method('isSatisfiedBy')
            ->with($this->event, '5555e6a0-94eb-480b-9b66-5b0041011d12')
            ->willReturn(true);

        $handler = new DeleteEventCommandHandler($this->eventRepository, $this->isEventOwnedByUser);

        $this->assertEmpty(($handler)($this->command));
    }

    public function testEventNotOwned(): void
    {
        $this->expectException(EventNotOwnedByUserException::class);

        $this->eventRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->with('f9c69d5b-6e68-44f0-86f2-a7e93136410c')
            ->willReturn($this->event);

        $this->isEventOwnedByUser
            ->expects(self::once())
            ->method('isSatisfiedBy')
            ->with($this->event, '5555e6a0-94eb-480b-9b66-5b0041011d12')
            ->willReturn(false);

        $handler = new DeleteEventCommandHandler($this->eventRepository, $this->isEventOwnedByUser);

        $this->assertEmpty(($handler)($this->command));
    }

    public function testEventNotFound(): void
    {
        $this->expectException(EventNotFoundException::class);

        $this->eventRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->with('f9c69d5b-6e68-44f0-86f2-a7e93136410c')
            ->willReturn(null);

        $this->isEventOwnedByUser
            ->expects(self::never())
            ->method('isSatisfiedBy');

        $handler = new DeleteEventCommandHandler($this->eventRepository, $this->isEventOwnedByUser);

        $this->assertEmpty(($handler)($this->command));
    }
}
