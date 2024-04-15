<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Participant\Specification;

use App\Domain\Event\Event;
use App\Domain\Participant\Participant;
use App\Domain\Participant\Repository\ParticipantRepositoryInterface;
use App\Domain\Participant\Specification\IsParticipantAlreadyRegistered;
use PHPUnit\Framework\TestCase;

final class IsParticipantAlreadyRegisteredTest extends TestCase
{
    public function testParticipantAlreadyExist(): void
    {
        $event = $this->createMock(Event::class);
        $participant = $this->createMock(Participant::class);
        $participantRepository = $this->createMock(ParticipantRepositoryInterface::class);
        $participantRepository
            ->expects(self::once())
            ->method('findOneByEventAndEmail')
            ->with($event, 'mathieu.marchois@gmail.com')
            ->willReturn($participant);

        $pattern = new IsParticipantAlreadyRegistered($participantRepository);
        $this->assertTrue($pattern->isSatisfiedBy($event, 'mathieu.marchois@gmail.com'));
    }

    public function tesParticipantDoesntExist(): void
    {
        $event = $this->createMock(Event::class);
        $participantRepository = $this->createMock(ParticipantRepositoryInterface::class);
        $participantRepository
            ->expects(self::once())
            ->method('findOneByEventAndEmail')
            ->with($event, 'mathieu.marchois@gmail.com')
            ->willReturn(null);

        $pattern = new IsParticipantAlreadyRegistered($participantRepository);
        $this->assertFalse($pattern->isSatisfiedBy($event, 'mathieu.marchois@gmail.com'));
    }
}
