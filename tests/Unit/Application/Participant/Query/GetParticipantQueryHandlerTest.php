<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Participant\Query;

use App\Application\Participant\Query\GetParticipantQuery;
use App\Application\Participant\Query\GetParticipantQueryHandler;
use App\Domain\Event\Event;
use App\Domain\Participant\Exception\ParticipantNotBelongsToEventException;
use App\Domain\Participant\Exception\ParticipantNotFoundException;
use App\Domain\Participant\Participant;
use App\Domain\Participant\Repository\ParticipantRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class GetParticipantQueryHandlerTest extends TestCase
{
    public function testGetParticipant(): void
    {
        $participantRepository = $this->createMock(ParticipantRepositoryInterface::class);
        $event = $this->createMock(Event::class);
        $participant = $this->createMock(Participant::class);
        $participant
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($event);

        $participantRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->with('37fa0f81-d9dd-4bbd-900b-9cc3b39d21e9')
            ->willReturn($participant);

        $query = new GetParticipantQuery(
            $event,
            '37fa0f81-d9dd-4bbd-900b-9cc3b39d21e9',
        );
        $handler = new GetParticipantQueryHandler($participantRepository);

        $this->assertEquals($participant, ($handler)($query));
    }

    public function testParticipantNotBelonsToEvent(): void
    {
        $this->expectException(ParticipantNotBelongsToEventException::class);

        $participantRepository = $this->createMock(ParticipantRepositoryInterface::class);
        $event = $this->createMock(Event::class);
        $event2 = $this->createMock(Event::class);

        $participant = $this->createMock(Participant::class);
        $participant
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($event2);

        $participantRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->with('37fa0f81-d9dd-4bbd-900b-9cc3b39d21e9')
            ->willReturn($participant);

        $query = new GetParticipantQuery(
            $event,
            '37fa0f81-d9dd-4bbd-900b-9cc3b39d21e9',
        );
        $handler = new GetParticipantQueryHandler($participantRepository);

        ($handler)($query);
    }

    public function testParticipantNotFound(): void
    {
        $this->expectException(ParticipantNotFoundException::class);

        $participantRepository = $this->createMock(ParticipantRepositoryInterface::class);
        $event = $this->createMock(Event::class);

        $participantRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->with('37fa0f81-d9dd-4bbd-900b-9cc3b39d21e9')
            ->willReturn(null);

        $query = new GetParticipantQuery(
            $event,
            '37fa0f81-d9dd-4bbd-900b-9cc3b39d21e9',
        );
        $handler = new GetParticipantQueryHandler($participantRepository);

        ($handler)($query);
    }
}
