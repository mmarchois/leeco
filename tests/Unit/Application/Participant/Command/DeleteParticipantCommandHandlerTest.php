<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Participant\Command;

use App\Application\Participant\Command\DeleteParticipantCommand;
use App\Application\Participant\Command\DeleteParticipantCommandHandler;
use App\Domain\Event\Event;
use App\Domain\Participant\Exception\ParticipantNotBelongsToEventException;
use App\Domain\Participant\Exception\ParticipantNotFoundException;
use App\Domain\Participant\Participant;
use App\Domain\Participant\Repository\ParticipantRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DeleteParticipantCommandHandlerTest extends TestCase
{
    private MockObject $participantRepository;
    private MockObject $event;
    private DeleteParticipantCommand $command;

    public function setUp(): void
    {
        $this->participantRepository = $this->createMock(ParticipantRepositoryInterface::class);
        $this->event = $this->createMock(Event::class);

        $command = new DeleteParticipantCommand($this->event, 'f9c69d5b-6e68-44f0-86f2-a7e93136410c');
        $this->command = $command;
    }

    public function testDelete(): void
    {
        $participant = $this->createMock(Participant::class);

        $this->participantRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->with('f9c69d5b-6e68-44f0-86f2-a7e93136410c')
            ->willReturn($participant);

        $participant
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($this->event);

        $handler = new DeleteParticipantCommandHandler($this->participantRepository);

        $this->assertEquals('f9c69d5b-6e68-44f0-86f2-a7e93136410c', ($handler)($this->command));
    }

    public function testDeleteParticipantNotBelongsToEvent(): void
    {
        $this->expectException(ParticipantNotBelongsToEventException::class);

        $participant = $this->createMock(Participant::class);
        $event = $this->createMock(Event::class);

        $this->participantRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->with('f9c69d5b-6e68-44f0-86f2-a7e93136410c')
            ->willReturn($participant);

        $participant
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($event);

        $handler = new DeleteParticipantCommandHandler($this->participantRepository);

        ($handler)($this->command);
    }

    public function testParticipantNotFound(): void
    {
        $this->expectException(ParticipantNotFoundException::class);

        $this->participantRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->with('f9c69d5b-6e68-44f0-86f2-a7e93136410c')
            ->willReturn(null);

        $this->participantRepository
            ->expects(self::never())
            ->method('delete');

        $handler = new DeleteParticipantCommandHandler($this->participantRepository);

        ($handler)($this->command);
    }
}
