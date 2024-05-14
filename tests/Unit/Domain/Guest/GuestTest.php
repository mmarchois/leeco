<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Guest;

use App\Domain\Event\Event;
use App\Domain\Guest\Guest;
use PHPUnit\Framework\TestCase;

final class GuestTest extends TestCase
{
    public function testGetters(): void
    {
        $event = $this->createMock(Event::class);
        $createdAt = new \DateTime('2023-01-01');

        $guest = new Guest(
            '9cebe00d-04d8-48da-89b1-059f6b7bfe44',
            'Mathieu',
            'Marchois',
            '9774d56d682e549c',
            $createdAt,
            $event,
        );

        $this->assertSame('9cebe00d-04d8-48da-89b1-059f6b7bfe44', $guest->getUuid());
        $this->assertSame('Mathieu', $guest->getFirstName());
        $this->assertSame('Marchois', $guest->getLastName());
        $this->assertSame('9774d56d682e549c', $guest->getDeviceIdentifier());
        $this->assertSame($event, $guest->getEvent());
        $this->assertSame($createdAt, $guest->getCreatedAt());
    }
}
