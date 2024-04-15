<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Participant\Command;

use App\Application\DateUtilsInterface;
use App\Application\IdFactoryInterface;
use App\Application\Participant\Command\SaveParticipantCommand;
use App\Application\Participant\Command\SaveParticipantCommandHandler;
use App\Domain\Event\Event;
use App\Domain\Event\Exception\EventNotFoundException;
use App\Domain\Event\Repository\EventRepositoryInterface;
use App\Domain\Participant\AccessCodeGenerator;
use App\Domain\Participant\Exception\ParticipantAlreadyExistException;
use App\Domain\Participant\Participant;
use App\Domain\Participant\Repository\ParticipantRepositoryInterface;
use App\Domain\Participant\Specification\IsParticipantAlreadyRegistered;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class SaveParticipantCommandHandlerTest extends TestCase
{
    private MockObject $idFactory;
    private MockObject $dateUtils;
    private MockObject $eventRepository;
    private MockObject $participantRepository;
    private MockObject $accessCodeGenerator;
    private MockObject $isParticipantAlreadyRegistered;
    private SaveParticipantCommand $command;

    public function setUp(): void
    {
        $this->idFactory = $this->createMock(IdFactoryInterface::class);
        $this->dateUtils = $this->createMock(DateUtilsInterface::class);
        $this->eventRepository = $this->createMock(EventRepositoryInterface::class);
        $this->participantRepository = $this->createMock(ParticipantRepositoryInterface::class);
        $this->accessCodeGenerator = $this->createMock(AccessCodeGenerator::class);
        $this->isParticipantAlreadyRegistered = $this->createMock(IsParticipantAlreadyRegistered::class);

        $command = new SaveParticipantCommand('3cd8a80c-1204-4f5d-82f8-4a7e98371f15');
        $command->firstName = 'Mathieu';
        $command->lastName = 'MARCHOIS';
        $command->email = '  mathieu.marchois@gmail.com   ';

        $this->command = $command;
    }

    public function testAdd(): void
    {
        $date = new \DateTimeImmutable('2024-04-15');
        $event = $this->createMock(Event::class);
        $createdParticipant = $this->createMock(Participant::class);
        $createdParticipant
            ->expects(self::once())
            ->method('getUuid')
            ->willReturn('94e78189-64bd-46f9-9b7e-d729475af557');

        $this->eventRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->with('3cd8a80c-1204-4f5d-82f8-4a7e98371f15')
            ->willReturn($event);

        $this->isParticipantAlreadyRegistered
            ->expects(self::once())
            ->method('isSatisfiedBy')
            ->with($event, 'mathieu.marchois@gmail.com')
            ->willReturn(false);

        $this->idFactory
            ->expects(self::once())
            ->method('make')
            ->willReturn('94e78189-64bd-46f9-9b7e-d729475af557');

        $this->accessCodeGenerator
            ->expects(self::once())
            ->method('generate')
            ->willReturn('accessCode');

        $this->dateUtils
            ->expects(self::once())
            ->method('getNow')
            ->willReturn($date);

        $this->participantRepository
            ->expects(self::once())
            ->method('add')
            ->with(
                new Participant(
                    uuid: '94e78189-64bd-46f9-9b7e-d729475af557',
                    firstName: 'Mathieu',
                    lastName: 'MARCHOIS',
                    email: 'mathieu.marchois@gmail.com',
                    accessCode: 'accessCode',
                    createdAt: $date,
                    event: $event,
                    accessSent: false,
                ),
            )
            ->willReturn($createdParticipant);

        $handler = new SaveParticipantCommandHandler(
            $this->idFactory,
            $this->dateUtils,
            $this->eventRepository,
            $this->participantRepository,
            $this->accessCodeGenerator,
            $this->isParticipantAlreadyRegistered,
        );

        $this->assertEquals('94e78189-64bd-46f9-9b7e-d729475af557', ($handler)($this->command));
    }

    public function testEventNotFound(): void
    {
        $this->expectException(EventNotFoundException::class);

        $this->eventRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->with('3cd8a80c-1204-4f5d-82f8-4a7e98371f15')
            ->willReturn(null);

        $this->isParticipantAlreadyRegistered
            ->expects(self::never())
            ->method('isSatisfiedBy');

        $this->idFactory
            ->expects(self::never())
            ->method('make');

        $this->accessCodeGenerator
            ->expects(self::never())
            ->method('generate');

        $this->dateUtils
            ->expects(self::never())
            ->method('getNow');

        $this->participantRepository
            ->expects(self::never())
            ->method('add');

        $handler = new SaveParticipantCommandHandler(
            $this->idFactory,
            $this->dateUtils,
            $this->eventRepository,
            $this->participantRepository,
            $this->accessCodeGenerator,
            $this->isParticipantAlreadyRegistered,
        );

        ($handler)($this->command);
    }

    public function testParticipantAlreadyRegistered(): void
    {
        $this->expectException(ParticipantAlreadyExistException::class);

        $event = $this->createMock(Event::class);
        $this->eventRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->with('3cd8a80c-1204-4f5d-82f8-4a7e98371f15')
            ->willReturn($event);

        $this->isParticipantAlreadyRegistered
            ->expects(self::once())
            ->method('isSatisfiedBy')
            ->with($event, 'mathieu.marchois@gmail.com')
            ->willReturn(true);

        $this->idFactory
            ->expects(self::never())
            ->method('make');

        $this->accessCodeGenerator
            ->expects(self::never())
            ->method('generate');

        $this->dateUtils
            ->expects(self::never())
            ->method('getNow');

        $this->participantRepository
            ->expects(self::never())
            ->method('add');

        $handler = new SaveParticipantCommandHandler(
            $this->idFactory,
            $this->dateUtils,
            $this->eventRepository,
            $this->participantRepository,
            $this->accessCodeGenerator,
            $this->isParticipantAlreadyRegistered,
        );

        ($handler)($this->command);
    }
}
