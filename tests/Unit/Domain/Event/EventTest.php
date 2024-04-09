<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Event;

use App\Domain\Event\Event;
use App\Domain\User\User;
use PHPUnit\Framework\TestCase;

final class EventTest extends TestCase
{
    public function testGetters(): void
    {
        $date = new \DateTime('2023-08-25 08:00:00');
        $expirationDate = new \DateTime('2023-09-17 18:00:00');
        $user = $this->createMock(User::class);

        $event = new Event(
            uuid: '9cebe00d-04d8-48da-89b1-059f6b7bfe44',
            title: 'Mariage H&M',
            date: $date,
            expirationDate: $expirationDate,
            owner: $user,
        );

        $this->assertSame('9cebe00d-04d8-48da-89b1-059f6b7bfe44', $event->getUuid());
        $this->assertSame('Mariage H&M', $event->getTitle());
        $this->assertSame($date, $event->getDate());
        $this->assertSame($expirationDate, $event->getExpirationDate());
        $this->assertSame($user, $event->getOwner());
    }
}
