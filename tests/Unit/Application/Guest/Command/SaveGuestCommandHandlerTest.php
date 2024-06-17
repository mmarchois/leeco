<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Guest\Command;

use App\Application\DateUtilsInterface;
use App\Application\Guest\Command\SaveGuestCommand;
use App\Application\Guest\Command\SaveGuestCommandHandler;
use App\Application\IdFactoryInterface;
use App\Domain\Event\Event;
use App\Domain\Event\Exception\EventNotFoundException;
use App\Domain\Event\Repository\EventRepositoryInterface;
use App\Domain\Guest\Exception\GuestAlreadyExistException;
use App\Domain\Guest\Guest;
use App\Domain\Guest\Repository\GuestRepositoryInterface;
use App\Domain\Guest\Specification\IsGuestAlreadyExist;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class SaveGuestCommandHandlerTest extends TestCase
{
    private MockObject $guestRepository;
    private MockObject $eventRepository;
    private MockObject $idFactory;
    private MockObject $dateUtils;
    private MockObject $isGuestAlreadyExist;
    private SaveGuestCommand $command;

    public function setUp(): void
    {
        $this->idFactory = $this->createMock(IdFactoryInterface::class);
        $this->guestRepository = $this->createMock(GuestRepositoryInterface::class);
        $this->eventRepository = $this->createMock(EventRepositoryInterface::class);
        $this->dateUtils = $this->createMock(DateUtilsInterface::class);
        $this->isGuestAlreadyExist = $this->createMock(IsGuestAlreadyExist::class);

        $command = new SaveGuestCommand(
            eventUuid: 'b4d5be0c-a760-4f53-aef3-9e8921be67c5',
            firstName: 'Mathieu',
            lastName: 'MARCHOIS',
            deviceIdentifier: '123456789',
        );

        $this->command = $command;
    }

    public function testSave(): void
    {
        $guest = $this->createMock(Guest::class);
        $event = $this->createMock(Event::class);
        $createdAt = new \DateTimeImmutable('2024-06-16');

        $this->idFactory
            ->expects(self::once())
            ->method('make')
            ->willReturn('e5abdfaf-f270-43e0-b0ec-c21a05342f03');

        $this->isGuestAlreadyExist
            ->expects(self::once())
            ->method('isSatisfiedBy')
            ->with('123456789', 'b4d5be0c-a760-4f53-aef3-9e8921be67c5')
            ->willReturn(false);

        $this->dateUtils
            ->expects(self::once())
            ->method('getNow')
            ->willReturn($createdAt);

        $this->eventRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->with('b4d5be0c-a760-4f53-aef3-9e8921be67c5')
            ->willReturn($event);

        $this->guestRepository
            ->expects(self::once())
            ->method('add')
            ->with(
                new Guest(
                    uuid: 'e5abdfaf-f270-43e0-b0ec-c21a05342f03',
                    firstName: 'Mathieu',
                    lastName: 'MARCHOIS',
                    deviceIdentifier: '123456789',
                    createdAt: $createdAt,
                    event: $event,
                ),
            )
            ->willReturn($guest);

        $guest
            ->expects(self::once())
            ->method('getUuid')
            ->willReturn('e5abdfaf-f270-43e0-b0ec-c21a05342f03');

        $handler = new SaveGuestCommandHandler(
            $this->idFactory,
            $this->guestRepository,
            $this->eventRepository,
            $this->dateUtils,
            $this->isGuestAlreadyExist,
        );

        $this->assertEquals('e5abdfaf-f270-43e0-b0ec-c21a05342f03', ($handler)($this->command));
    }

    public function testGuestAlreadyExist(): void
    {
        $this->expectException(GuestAlreadyExistException::class);

        $event = $this->createMock(Event::class);

        $this->idFactory
            ->expects(self::never())
            ->method('make');

        $this->isGuestAlreadyExist
            ->expects(self::once())
            ->method('isSatisfiedBy')
            ->with('123456789', 'b4d5be0c-a760-4f53-aef3-9e8921be67c5')
            ->willReturn(true);

        $this->dateUtils
            ->expects(self::never())
            ->method('getNow');

        $this->eventRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->with('b4d5be0c-a760-4f53-aef3-9e8921be67c5')
            ->willReturn($event);

        $this->guestRepository
            ->expects(self::never())
            ->method('add');

        $handler = new SaveGuestCommandHandler(
            $this->idFactory,
            $this->guestRepository,
            $this->eventRepository,
            $this->dateUtils,
            $this->isGuestAlreadyExist,
        );

        ($handler)($this->command);
    }

    public function testEventNotFound(): void
    {
        $this->expectException(EventNotFoundException::class);

        $this->idFactory
            ->expects(self::never())
            ->method('make');

        $this->isGuestAlreadyExist
            ->expects(self::never())
            ->method('isSatisfiedBy');

        $this->dateUtils
            ->expects(self::never())
            ->method('getNow');

        $this->eventRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->with('b4d5be0c-a760-4f53-aef3-9e8921be67c5')
            ->willReturn(null);

        $this->guestRepository
            ->expects(self::never())
            ->method('add');

        $handler = new SaveGuestCommandHandler(
            $this->idFactory,
            $this->guestRepository,
            $this->eventRepository,
            $this->dateUtils,
            $this->isGuestAlreadyExist,
        );

        ($handler)($this->command);
    }
}
