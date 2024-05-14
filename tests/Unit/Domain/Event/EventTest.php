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
        $startDate = new \DateTime('2023-08-25 08:00:00');
        $endDate = new \DateTime('2023-08-26 08:00:00');
        $startDate2 = new \DateTime('2023-09-17 18:00:00');
        $endDate2 = new \DateTime('2023-09-18 18:00:00');
        $user = $this->createMock(User::class);

        $event = new Event(
            uuid: '9cebe00d-04d8-48da-89b1-059f6b7bfe44',
            title: 'Mariage H&M',
            accessCode: 'FR367876',
            startDate: $startDate,
            endDate: $endDate,
            owner: $user,
        );

        $this->assertSame('9cebe00d-04d8-48da-89b1-059f6b7bfe44', $event->getUuid());
        $this->assertSame('Mariage H&M', $event->getTitle());
        $this->assertSame($startDate, $event->getStartDate());
        $this->assertSame($endDate, $event->getEndDate());
        $this->assertSame($user, $event->getOwner());
        $this->assertSame('FR367876', $event->getAccessCode());

        $event->update('Mariage A&A', $startDate2, $endDate2);

        $this->assertSame('Mariage A&A', $event->getTitle());
        $this->assertSame($startDate2, $event->getStartDate());
        $this->assertSame($endDate2, $event->getEndDate());
    }
}
