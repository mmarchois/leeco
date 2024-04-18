<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Event\Command;

use App\Application\DateUtilsInterface;
use App\Application\Event\Command\SaveEventCommand;
use App\Application\Event\Command\SaveEventCommandHandler;
use App\Application\IdFactoryInterface;
use App\Domain\Event\Event;
use App\Domain\Event\Exception\EventAlreadyExistException;
use App\Domain\Event\Repository\EventRepositoryInterface;
use App\Domain\Event\Specification\IsEventAlreadyExist;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class SaveEventCommandHandlerTest extends TestCase
{
    private MockObject $idFactory;
    private MockObject $dateUtils;
    private MockObject $userRepository;
    private MockObject $eventRepository;
    private MockObject $isEventAlreadyExist;

    public function setUp(): void
    {
        $this->idFactory = $this->createMock(IdFactoryInterface::class);
        $this->dateUtils = $this->createMock(DateUtilsInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->eventRepository = $this->createMock(EventRepositoryInterface::class);
        $this->isEventAlreadyExist = $this->createMock(IsEventAlreadyExist::class);
    }

    public function testCreate(): void
    {
        $date = new \DateTime('2023-01-01');
        $expirationDate = new \DateTimeImmutable('2023-01-30');

        $createdEvent = $this->createMock(Event::class);
        $createdEvent
            ->expects(self::once())
            ->method('getUuid')
            ->willReturn('fc9df7ca-d73c-4e5d-a889-fd4833a4116e');

        $user = $this->createMock(User::class);

        $this->isEventAlreadyExist
            ->expects(self::once())
            ->method('isSatisfiedBy')
            ->with('91340bb8-50d7-4d88-bcd6-bb2612ae5557', 'Mariage H&M')
            ->willReturn(false);

        $this->userRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->with('91340bb8-50d7-4d88-bcd6-bb2612ae5557')
            ->willReturn($user);

        $this->dateUtils
            ->expects(self::once())
            ->method('addDaysToDate')
            ->with($date, 30)
            ->willReturn($expirationDate);

        $this->idFactory
            ->expects(self::once())
            ->method('make')
            ->willReturn('fc9df7ca-d73c-4e5d-a889-fd4833a4116e');

        $this->eventRepository
            ->expects(self::once())
            ->method('add')
            ->with(
                new Event(
                    uuid: 'fc9df7ca-d73c-4e5d-a889-fd4833a4116e',
                    title: 'Mariage H&M',
                    date: $date,
                    expirationDate: $expirationDate,
                    owner: $user,
                ),
            )
            ->willReturn($createdEvent);

        $this->eventRepository
            ->expects(self::never())
            ->method('findOneByUuid');

        $createdEvent
            ->expects(self::never())
            ->method('update');

        $command = new SaveEventCommand('91340bb8-50d7-4d88-bcd6-bb2612ae5557');
        $command->title = '  Mariage H&M  '; // Voluntary add spaces
        $command->date = $date;

        $handler = new SaveEventCommandHandler(
            $this->idFactory,
            $this->dateUtils,
            $this->userRepository,
            $this->eventRepository,
            $this->isEventAlreadyExist,
        );

        $this->assertSame('fc9df7ca-d73c-4e5d-a889-fd4833a4116e', ($handler)($command));
    }

    public function testCreateEventAlreadyExist(): void
    {
        $this->expectException(EventAlreadyExistException::class);

        $date = new \DateTime('2023-01-01');

        $this->isEventAlreadyExist
            ->expects(self::once())
            ->method('isSatisfiedBy')
            ->with('91340bb8-50d7-4d88-bcd6-bb2612ae5557', 'Mariage H&M')
            ->willReturn(true);

        $this->userRepository
            ->expects(self::never())
            ->method('findOneByUuid');

        $this->dateUtils
            ->expects(self::once())
            ->method('addDaysToDate');

        $this->idFactory
            ->expects(self::never())
            ->method('make');

        $this->eventRepository
            ->expects(self::never())
            ->method('add');

        $this->eventRepository
            ->expects(self::never())
            ->method('findOneByUuid');

        $command = new SaveEventCommand('91340bb8-50d7-4d88-bcd6-bb2612ae5557');
        $command->title = '  Mariage H&M  '; // Voluntary add spaces
        $command->date = $date;

        $handler = new SaveEventCommandHandler(
            $this->idFactory,
            $this->dateUtils,
            $this->userRepository,
            $this->eventRepository,
            $this->isEventAlreadyExist,
        );

        ($handler)($command);
    }

    public function testCreateUserNotFound(): void
    {
        $this->expectException(UserNotFoundException::class);

        $date = new \DateTime('2023-01-01');

        $this->isEventAlreadyExist
            ->expects(self::once())
            ->method('isSatisfiedBy')
            ->with('91340bb8-50d7-4d88-bcd6-bb2612ae5557', 'Mariage H&M')
            ->willReturn(false);

        $this->userRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->willReturn(null);

        $this->dateUtils
            ->expects(self::once())
            ->method('addDaysToDate');

        $this->idFactory
            ->expects(self::never())
            ->method('make');

        $this->eventRepository
            ->expects(self::never())
            ->method('add');

        $this->eventRepository
            ->expects(self::never())
            ->method('findOneByUuid');

        $command = new SaveEventCommand('91340bb8-50d7-4d88-bcd6-bb2612ae5557');
        $command->title = '  Mariage H&M  '; // Voluntary add spaces
        $command->date = $date;

        $handler = new SaveEventCommandHandler(
            $this->idFactory,
            $this->dateUtils,
            $this->userRepository,
            $this->eventRepository,
            $this->isEventAlreadyExist,
        );

        ($handler)($command);
    }

    public function testUpdateWithDifferentTitle(): void
    {
        $date = new \DateTime('2023-01-01');
        $expirationDate = new \DateTimeImmutable('2023-01-30');

        $event = $this->createMock(Event::class);
        $event
            ->expects(self::once())
            ->method('getTitle')
            ->willReturn('Mariage H&M');
        $event
            ->expects(self::once())
            ->method('update')
            ->with('Mariage A&A', $date, $expirationDate);

        $this->isEventAlreadyExist
            ->expects(self::once())
            ->method('isSatisfiedBy')
            ->with('91340bb8-50d7-4d88-bcd6-bb2612ae5557', 'Mariage A&A')
            ->willReturn(false);

        $this->userRepository
            ->expects(self::never())
            ->method('findOneByUuid');

        $this->dateUtils
            ->expects(self::once())
            ->method('addDaysToDate')
            ->with($date, 30)
            ->willReturn($expirationDate);

        $this->idFactory
            ->expects(self::never())
            ->method('make');

        $this->eventRepository
            ->expects(self::never())
            ->method('add');

        $command = new SaveEventCommand('91340bb8-50d7-4d88-bcd6-bb2612ae5557', $event);
        $command->uuid = 'fc9df7ca-d73c-4e5d-a889-fd4833a4116e';
        $command->title = '  Mariage A&A  '; // Voluntary add spaces
        $command->date = $date;

        $handler = new SaveEventCommandHandler(
            $this->idFactory,
            $this->dateUtils,
            $this->userRepository,
            $this->eventRepository,
            $this->isEventAlreadyExist,
        );

        $this->assertSame('fc9df7ca-d73c-4e5d-a889-fd4833a4116e', ($handler)($command));
    }

    public function testUpdateWithSameTitle(): void
    {
        $date = new \DateTime('2023-01-01');
        $expirationDate = new \DateTimeImmutable('2023-01-30');

        $event = $this->createMock(Event::class);
        $event
            ->expects(self::once())
            ->method('getTitle')
            ->willReturn('Mariage H&M');
        $event
            ->expects(self::once())
            ->method('update')
            ->with('Mariage H&M', $date, $expirationDate);

        $this->isEventAlreadyExist
            ->expects(self::never())
            ->method('isSatisfiedBy');

        $this->userRepository
            ->expects(self::never())
            ->method('findOneByUuid');

        $this->dateUtils
            ->expects(self::once())
            ->method('addDaysToDate')
            ->with($date, 30)
            ->willReturn($expirationDate);

        $this->idFactory
            ->expects(self::never())
            ->method('make');

        $this->eventRepository
            ->expects(self::never())
            ->method('add');

        $command = new SaveEventCommand('91340bb8-50d7-4d88-bcd6-bb2612ae5557', $event);
        $command->uuid = 'fc9df7ca-d73c-4e5d-a889-fd4833a4116e';
        $command->title = '  Mariage H&M  '; // Voluntary add spaces
        $command->date = $date;

        $handler = new SaveEventCommandHandler(
            $this->idFactory,
            $this->dateUtils,
            $this->userRepository,
            $this->eventRepository,
            $this->isEventAlreadyExist,
        );

        $this->assertSame('fc9df7ca-d73c-4e5d-a889-fd4833a4116e', ($handler)($command));
    }

    public function testUpdateWithDifferentTitleThatAlreadyExist(): void
    {
        $this->expectException(EventAlreadyExistException::class);

        $date = new \DateTime('2023-01-01');
        $expirationDate = new \DateTimeImmutable('2023-01-30');

        $event = $this->createMock(Event::class);
        $event
            ->expects(self::once())
            ->method('getTitle')
            ->willReturn('Mariage H&M');
        $event
            ->expects(self::never())
            ->method('update');

        $this->isEventAlreadyExist
            ->expects(self::once())
            ->method('isSatisfiedBy')
            ->with('91340bb8-50d7-4d88-bcd6-bb2612ae5557', 'Mariage A&A')
            ->willReturn(true);

        $this->userRepository
            ->expects(self::never())
            ->method('findOneByUuid');

        $this->dateUtils
            ->expects(self::once())
            ->method('addDaysToDate')
            ->with($date, 30)
            ->willReturn($expirationDate);

        $this->idFactory
            ->expects(self::never())
            ->method('make');

        $this->eventRepository
            ->expects(self::never())
            ->method('add');

        $command = new SaveEventCommand('91340bb8-50d7-4d88-bcd6-bb2612ae5557', $event);
        $command->uuid = 'fc9df7ca-d73c-4e5d-a889-fd4833a4116e';
        $command->title = '  Mariage A&A  '; // Voluntary add spaces
        $command->date = $date;

        $handler = new SaveEventCommandHandler(
            $this->idFactory,
            $this->dateUtils,
            $this->userRepository,
            $this->eventRepository,
            $this->isEventAlreadyExist,
        );

        ($handler)($command);
    }
}
