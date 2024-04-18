<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Event\Specification;

use App\Domain\Event\Event;
use App\Domain\Event\Specification\IsEventOwnedByUser;
use App\Domain\User\User;
use PHPUnit\Framework\TestCase;

final class IsEventOwnedByUserTest extends TestCase
{
    public function testEventOwned(): void
    {
        $user = $this->createMock(User::class);
        $user
            ->expects(self::once())
            ->method('getUuid')
            ->willReturn('6202205c-d16f-4dc8-b8da-b3cd31539a08');

        $event = $this->createMock(Event::class);
        $event
            ->expects(self::once())
            ->method('getOwner')
            ->willReturn($user);

        $pattern = new IsEventOwnedByUser();
        $this->assertTrue($pattern->isSatisfiedBy($event, '6202205c-d16f-4dc8-b8da-b3cd31539a08'));
    }

    public function testNotEventOwned(): void
    {
        $user = $this->createMock(User::class);
        $user
            ->expects(self::once())
            ->method('getUuid')
            ->willReturn('6202205c-d16f-4dc8-b8da-b3cd31539a08');

        $event = $this->createMock(Event::class);
        $event
            ->expects(self::once())
            ->method('getOwner')
            ->willReturn($user);

        $pattern = new IsEventOwnedByUser();
        $this->assertFalse($pattern->isSatisfiedBy($event, 'd6ba285b-7bc0-4388-890d-84d406a9d029'));
    }
}
