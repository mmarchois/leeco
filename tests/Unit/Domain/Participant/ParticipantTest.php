<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Participant;

use App\Domain\Event\Event;
use App\Domain\Participant\Participant;
use PHPUnit\Framework\TestCase;

final class ParticipantTest extends TestCase
{
    public function testGetters(): void
    {
        $event = $this->createMock(Event::class);
        $createdAt = new \DateTime('2023-01-01');

        $participant = new Participant(
            '9cebe00d-04d8-48da-89b1-059f6b7bfe44',
            'Mathieu',
            'Marchois',
            'mathieu.marchois@gmail.com',
            'acces_code',
            $createdAt,
            $event,
            false,
        );

        $this->assertSame('9cebe00d-04d8-48da-89b1-059f6b7bfe44', $participant->getUuid());
        $this->assertSame('Mathieu', $participant->getFirstName());
        $this->assertSame('Marchois', $participant->getLastName());
        $this->assertSame('mathieu.marchois@gmail.com', $participant->getEmail());
        $this->assertSame('acces_code', $participant->getAccessCode());
        $this->assertSame($event, $participant->getEvent());
        $this->assertSame($createdAt, $participant->getCreatedAt());
        $this->assertFalse($participant->isAccessSent());

        $participant->update('Floran', 'Roisin', 'floran.roisin@gmail.com');
        $this->assertSame('Floran', $participant->getFirstName());
        $this->assertSame('Roisin', $participant->getLastName());
        $this->assertSame('floran.roisin@gmail.com', $participant->getEmail());
    }
}
