<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Participant\Command;

use App\Application\DateUtilsInterface;
use App\Application\IdFactoryInterface;
use App\Application\Participant\Command\SaveParticipantCommand;
use App\Application\Participant\Command\SaveParticipantCommandHandler;
use App\Domain\Event\Event;
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
    private MockObject $participantRepository;
    private MockObject $accessCodeGenerator;
    private MockObject $isParticipantAlreadyRegistered;
    private MockObject $event;
    private SaveParticipantCommand $command;

    public function setUp(): void
    {
        $this->idFactory = $this->createMock(IdFactoryInterface::class);
        $this->dateUtils = $this->createMock(DateUtilsInterface::class);
        $this->participantRepository = $this->createMock(ParticipantRepositoryInterface::class);
        $this->accessCodeGenerator = $this->createMock(AccessCodeGenerator::class);
        $this->isParticipantAlreadyRegistered = $this->createMock(IsParticipantAlreadyRegistered::class);
        $this->event = $this->createMock(Event::class);

        $command = new SaveParticipantCommand($this->event);
        $command->firstName = 'Mathieu';
        $command->lastName = 'MARCHOIS';
        $command->email = '  mathieu.marchois@gmail.com   '; // Voluntary add spaces

        $this->command = $command;
    }

    public function testAdd(): void
    {
        $date = new \DateTimeImmutable('2024-04-15');
        $createdParticipant = $this->createMock(Participant::class);
        $createdParticipant
            ->expects(self::once())
            ->method('getUuid')
            ->willReturn('94e78189-64bd-46f9-9b7e-d729475af557');

        $this->isParticipantAlreadyRegistered
            ->expects(self::once())
            ->method('isSatisfiedBy')
            ->with($this->event, 'mathieu.marchois@gmail.com')
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
                    event: $this->event,
                    accessSent: false,
                ),
            )
            ->willReturn($createdParticipant);

        $handler = new SaveParticipantCommandHandler(
            $this->idFactory,
            $this->dateUtils,
            $this->participantRepository,
            $this->accessCodeGenerator,
            $this->isParticipantAlreadyRegistered,
        );

        $this->assertEquals('94e78189-64bd-46f9-9b7e-d729475af557', ($handler)($this->command));
    }

    public function testParticipantAlreadyRegistered(): void
    {
        $this->expectException(ParticipantAlreadyExistException::class);

        $this->isParticipantAlreadyRegistered
            ->expects(self::once())
            ->method('isSatisfiedBy')
            ->with($this->event, 'mathieu.marchois@gmail.com')
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
            $this->participantRepository,
            $this->accessCodeGenerator,
            $this->isParticipantAlreadyRegistered,
        );

        ($handler)($this->command);
    }

    public function testUpdateEmailDoesntChanged(): void
    {
        $participant = $this->createMock(Participant::class);
        $participant
            ->expects(self::once())
            ->method('getEmail')
            ->willReturn('mathieu.marchois@gmail.com');
        $participant
            ->expects(self::once())
            ->method('getUuid')
            ->willReturn('94e78189-64bd-46f9-9b7e-d729475af557');
        $participant
            ->expects(self::once())
            ->method('update')
            ->with('Mathieuu', 'Marchoiss', 'mathieu.marchois@gmail.com');

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
            $this->participantRepository,
            $this->accessCodeGenerator,
            $this->isParticipantAlreadyRegistered,
        );

        $command = new SaveParticipantCommand($this->event, $participant);
        $command->firstName = 'Mathieuu';
        $command->lastName = 'Marchoiss';
        $command->email = '  mathIeu.Marchois@gmail.com   '; // Voluntary add spaces

        $this->assertEquals('94e78189-64bd-46f9-9b7e-d729475af557', ($handler)($command));
    }

    public function testUpdateEmailChanged(): void
    {
        $participant = $this->createMock(Participant::class);
        $participant
            ->expects(self::once())
            ->method('getEmail')
            ->willReturn('mathieu@gmail.com');
        $participant
            ->expects(self::once())
            ->method('getUuid')
            ->willReturn('94e78189-64bd-46f9-9b7e-d729475af557');
        $participant
            ->expects(self::once())
            ->method('update')
            ->with('Mathieuu', 'Marchoiss', 'mathieu.marchois@gmail.com');

        $this->isParticipantAlreadyRegistered
            ->expects(self::once())
            ->method('isSatisfiedBy')
            ->with($this->event, 'mathieu.marchois@gmail.com')
            ->willReturn(false);

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
            $this->participantRepository,
            $this->accessCodeGenerator,
            $this->isParticipantAlreadyRegistered,
        );

        $command = new SaveParticipantCommand($this->event, $participant);
        $command->firstName = 'Mathieuu';
        $command->lastName = 'Marchoiss';
        $command->email = '  mathIeu.Marchois@gmail.com   '; // Voluntary add spaces

        $this->assertEquals('94e78189-64bd-46f9-9b7e-d729475af557', ($handler)($command));
    }

    public function testUpdateParticipantAlreadyExist(): void
    {
        $this->expectException(ParticipantAlreadyExistException::class);

        $participant = $this->createMock(Participant::class);
        $participant
            ->expects(self::once())
            ->method('getEmail')
            ->willReturn('mathieu@gmail.com');
        $participant
            ->expects(self::never())
            ->method('getUuid');
        $participant
            ->expects(self::never())
            ->method('update');

        $this->isParticipantAlreadyRegistered
            ->expects(self::once())
            ->method('isSatisfiedBy')
            ->with($this->event, 'mathieu.marchois@gmail.com')
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
            $this->participantRepository,
            $this->accessCodeGenerator,
            $this->isParticipantAlreadyRegistered,
        );

        $command = new SaveParticipantCommand($this->event, $participant);
        $command->firstName = 'Mathieuu';
        $command->lastName = 'Marchoiss';
        $command->email = '  mathIeu.Marchois@gmail.com   '; // Voluntary add spaces

        $this->assertEquals('94e78189-64bd-46f9-9b7e-d729475af557', ($handler)($command));
    }
}
