<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Event\Command;

use App\Application\CommandBusInterface;
use App\Application\Event\Command\SaveEventCommand;
use App\Application\Event\Command\SaveEventCommandHandler;
use App\Application\IdFactoryInterface;
use App\Application\Media\Command\SaveMediaCommand;
use App\Domain\Event\AccessCodeGenerator;
use App\Domain\Event\Event;
use App\Domain\Event\Exception\EventAlreadyExistException;
use App\Domain\Event\Repository\EventRepositoryInterface;
use App\Domain\Event\Specification\IsEventAlreadyExist;
use App\Domain\Media\Media;
use App\Domain\Media\MediaTypeEnum;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class SaveEventCommandHandlerTest extends TestCase
{
    private MockObject $idFactory;
    private MockObject $userRepository;
    private MockObject $eventRepository;
    private MockObject $isEventAlreadyExist;
    private MockObject $accessCodeGenerator;
    private MockObject $commandBus;

    public function setUp(): void
    {
        $this->idFactory = $this->createMock(IdFactoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->eventRepository = $this->createMock(EventRepositoryInterface::class);
        $this->isEventAlreadyExist = $this->createMock(IsEventAlreadyExist::class);
        $this->accessCodeGenerator = $this->createMock(AccessCodeGenerator::class);
        $this->commandBus = $this->createMock(CommandBusInterface::class);
    }

    public function testCreate(): void
    {
        $startDate = new \DateTime('2023-01-01');
        $endDate = new \DateTimeImmutable('2023-01-01');

        $user = $this->createMock(User::class);

        $createdEvent = $this->createMock(Event::class);
        $createdEvent
            ->expects(self::once())
            ->method('getUuid')
            ->willReturn('fc9df7ca-d73c-4e5d-a889-fd4833a4116e');
        $createdEvent
            ->expects(self::never())
            ->method('updateMedia');

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

        $this->idFactory
            ->expects(self::once())
            ->method('make')
            ->willReturn('fc9df7ca-d73c-4e5d-a889-fd4833a4116e');

        $this->commandBus
            ->expects(self::never())
            ->method('handle');

        $this->accessCodeGenerator
            ->expects(self::once())
            ->method('generate')
            ->willReturn('FR87898778');

        $this->eventRepository
            ->expects(self::once())
            ->method('add')
            ->with(
                new Event(
                    uuid: 'fc9df7ca-d73c-4e5d-a889-fd4833a4116e',
                    title: 'Mariage H&M',
                    accessCode: 'FR87898778',
                    startDate: $startDate,
                    endDate: $endDate,
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
        $command->startDate = $startDate;
        $command->endDate = $endDate;

        $handler = new SaveEventCommandHandler(
            $this->idFactory,
            $this->userRepository,
            $this->eventRepository,
            $this->isEventAlreadyExist,
            $this->accessCodeGenerator,
            $this->commandBus,
        );

        $this->assertSame('fc9df7ca-d73c-4e5d-a889-fd4833a4116e', ($handler)($command));
    }

    public function testCreateWithFile(): void
    {
        $startDate = new \DateTime('2023-01-01');
        $endDate = new \DateTimeImmutable('2023-01-01');

        $file = $this->createMock(UploadedFile::class);
        $createdMedia = $this->createMock(Media::class);
        $user = $this->createMock(User::class);

        $createdEvent = $this->createMock(Event::class);
        $createdEvent
            ->expects(self::once())
            ->method('getUuid')
            ->willReturn('fc9df7ca-d73c-4e5d-a889-fd4833a4116e');
        $createdEvent
            ->expects(self::once())
            ->method('updateMedia')
            ->with($createdMedia);

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

        $this->idFactory
            ->expects(self::once())
            ->method('make')
            ->willReturn('fc9df7ca-d73c-4e5d-a889-fd4833a4116e');

        $this->commandBus
            ->expects(self::once())
            ->method('handle')
            ->with(
                $this->equalTo(
                    new SaveMediaCommand(
                        event: $createdEvent,
                        file: $file,
                        type: MediaTypeEnum::EVENT_BANNER->value,
                        media: null,
                    ),
                ),
            )
            ->willReturn($createdMedia);

        $this->accessCodeGenerator
            ->expects(self::once())
            ->method('generate')
            ->willReturn('FR87898778');

        $this->eventRepository
            ->expects(self::once())
            ->method('add')
            ->with(
                new Event(
                    uuid: 'fc9df7ca-d73c-4e5d-a889-fd4833a4116e',
                    title: 'Mariage H&M',
                    accessCode: 'FR87898778',
                    startDate: $startDate,
                    endDate: $endDate,
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
        $command->startDate = $startDate;
        $command->endDate = $endDate;
        $command->file = $file;

        $handler = new SaveEventCommandHandler(
            $this->idFactory,
            $this->userRepository,
            $this->eventRepository,
            $this->isEventAlreadyExist,
            $this->accessCodeGenerator,
            $this->commandBus,
        );

        $this->assertSame('fc9df7ca-d73c-4e5d-a889-fd4833a4116e', ($handler)($command));
    }

    public function testCreateEventAlreadyExist(): void
    {
        $this->expectException(EventAlreadyExistException::class);

        $this->isEventAlreadyExist
            ->expects(self::once())
            ->method('isSatisfiedBy')
            ->with('91340bb8-50d7-4d88-bcd6-bb2612ae5557', 'Mariage H&M')
            ->willReturn(true);

        $this->userRepository
            ->expects(self::never())
            ->method('findOneByUuid');

        $this->idFactory
            ->expects(self::never())
            ->method('make');

        $this->commandBus
            ->expects(self::never())
            ->method('handle');

        $this->accessCodeGenerator
            ->expects(self::never())
            ->method('generate');

        $this->eventRepository
            ->expects(self::never())
            ->method('add');

        $this->eventRepository
            ->expects(self::never())
            ->method('findOneByUuid');

        $command = new SaveEventCommand('91340bb8-50d7-4d88-bcd6-bb2612ae5557');
        $command->title = '  Mariage H&M  '; // Voluntary add spaces

        $handler = new SaveEventCommandHandler(
            $this->idFactory,
            $this->userRepository,
            $this->eventRepository,
            $this->isEventAlreadyExist,
            $this->accessCodeGenerator,
            $this->commandBus,
        );

        ($handler)($command);
    }

    public function testCreateUserNotFound(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->isEventAlreadyExist
            ->expects(self::once())
            ->method('isSatisfiedBy')
            ->with('91340bb8-50d7-4d88-bcd6-bb2612ae5557', 'Mariage H&M')
            ->willReturn(false);

        $this->userRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->willReturn(null);

        $this->idFactory
            ->expects(self::never())
            ->method('make');

        $this->commandBus
            ->expects(self::never())
            ->method('handle');

        $this->eventRepository
            ->expects(self::never())
            ->method('add');

        $this->eventRepository
            ->expects(self::never())
            ->method('findOneByUuid');

        $this->accessCodeGenerator
            ->expects(self::never())
            ->method('generate');

        $command = new SaveEventCommand('91340bb8-50d7-4d88-bcd6-bb2612ae5557');
        $command->title = '  Mariage H&M  '; // Voluntary add spaces

        $handler = new SaveEventCommandHandler(
            $this->idFactory,
            $this->userRepository,
            $this->eventRepository,
            $this->isEventAlreadyExist,
            $this->accessCodeGenerator,
            $this->commandBus,
        );

        ($handler)($command);
    }

    public function testUpdateWithDifferentTitle(): void
    {
        $startDate = new \DateTime('2023-01-01');
        $endDate = new \DateTimeImmutable('2023-01-06');

        $event = $this->createMock(Event::class);
        $event
            ->expects(self::once())
            ->method('getTitle')
            ->willReturn('Mariage H&M');
        $event
            ->expects(self::never())
            ->method('updateMedia');

        $event
            ->expects(self::once())
            ->method('update')
            ->with('Mariage A&A', $startDate, $endDate);

        $this->isEventAlreadyExist
            ->expects(self::once())
            ->method('isSatisfiedBy')
            ->with('91340bb8-50d7-4d88-bcd6-bb2612ae5557', 'Mariage A&A')
            ->willReturn(false);

        $this->userRepository
            ->expects(self::never())
            ->method('findOneByUuid');

        $this->commandBus
            ->expects(self::never())
            ->method('handle');

        $this->idFactory
            ->expects(self::never())
            ->method('make');

        $this->eventRepository
            ->expects(self::never())
            ->method('add');

        $this->accessCodeGenerator
            ->expects(self::never())
            ->method('generate');

        $command = new SaveEventCommand('91340bb8-50d7-4d88-bcd6-bb2612ae5557', $event);
        $command->uuid = 'fc9df7ca-d73c-4e5d-a889-fd4833a4116e';
        $command->title = '  Mariage A&A  '; // Voluntary add spaces
        $command->startDate = $startDate;
        $command->endDate = $endDate;

        $handler = new SaveEventCommandHandler(
            $this->idFactory,
            $this->userRepository,
            $this->eventRepository,
            $this->isEventAlreadyExist,
            $this->accessCodeGenerator,
            $this->commandBus,
        );

        $this->assertSame('fc9df7ca-d73c-4e5d-a889-fd4833a4116e', ($handler)($command));
    }

    public function testUpdateWithSameTitleAndFile(): void
    {
        $startDate = new \DateTime('2023-01-01');
        $endDate = new \DateTimeImmutable('2023-01-30');

        $media = $this->createMock(Media::class);
        $file = $this->createMock(UploadedFile::class);
        $event = $this->createMock(Event::class);
        $event
            ->expects(self::once())
            ->method('getTitle')
            ->willReturn('Mariage H&M');
        $event
            ->expects(self::once())
            ->method('getMedia')
            ->willReturn($media);
        $event
            ->expects(self::once())
            ->method('update')
            ->with('Mariage H&M', $startDate, $endDate);
        $event
            ->expects(self::once())
            ->method('updateMedia')
            ->with($media);

        $this->isEventAlreadyExist
            ->expects(self::never())
            ->method('isSatisfiedBy');

        $this->userRepository
            ->expects(self::never())
            ->method('findOneByUuid');

        $this->commandBus
            ->expects(self::once())
            ->method('handle')
            ->with(
                $this->equalTo(
                    new SaveMediaCommand(
                        event: $event,
                        file: $file,
                        type: MediaTypeEnum::EVENT_BANNER->value,
                        media: $media,
                    ),
                ),
            )
            ->willReturn($media);

        $this->idFactory
            ->expects(self::never())
            ->method('make');

        $this->eventRepository
            ->expects(self::never())
            ->method('add');

        $this->accessCodeGenerator
            ->expects(self::never())
            ->method('generate');

        $command = new SaveEventCommand('91340bb8-50d7-4d88-bcd6-bb2612ae5557', $event);
        $command->uuid = 'fc9df7ca-d73c-4e5d-a889-fd4833a4116e';
        $command->title = '  Mariage H&M  '; // Voluntary add spaces
        $command->startDate = $startDate;
        $command->endDate = $endDate;
        $command->file = $file;

        $handler = new SaveEventCommandHandler(
            $this->idFactory,
            $this->userRepository,
            $this->eventRepository,
            $this->isEventAlreadyExist,
            $this->accessCodeGenerator,
            $this->commandBus,
        );

        $this->assertSame('fc9df7ca-d73c-4e5d-a889-fd4833a4116e', ($handler)($command));
    }

    public function testUpdateWithDifferentTitleThatAlreadyExist(): void
    {
        $this->expectException(EventAlreadyExistException::class);

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

        $this->idFactory
            ->expects(self::never())
            ->method('make');

        $this->eventRepository
            ->expects(self::never())
            ->method('add');

        $this->accessCodeGenerator
            ->expects(self::never())
            ->method('generate');

        $command = new SaveEventCommand('91340bb8-50d7-4d88-bcd6-bb2612ae5557', $event);
        $command->uuid = 'fc9df7ca-d73c-4e5d-a889-fd4833a4116e';
        $command->title = '  Mariage A&A  '; // Voluntary add spaces

        $handler = new SaveEventCommandHandler(
            $this->idFactory,
            $this->userRepository,
            $this->eventRepository,
            $this->isEventAlreadyExist,
            $this->accessCodeGenerator,
            $this->commandBus,
        );

        ($handler)($command);
    }
}
