<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Guest\Query;

use App\Application\Guest\Query\GetGuestsByEventQuery;
use App\Application\Guest\Query\GetGuestsByEventQueryHandler;
use App\Application\Guest\View\GuestView;
use App\Domain\Guest\Repository\GuestRepositoryInterface;
use App\Domain\Pagination;
use PHPUnit\Framework\TestCase;

final class GetGuestsByEventQueryHandlerTest extends TestCase
{
    public function testGuests(): void
    {
        $guestRepository = $this->createMock(GuestRepositoryInterface::class);
        $guests = [
            new GuestView(
                '33795ae7-395a-440a-9fa8-f72343d62eb0',
                'Mathieu',
                'MARCHOIS',
                new \DateTime('2024-05-13'),
            ),
            new GuestView(
                'e8e165c3-64bf-46e5-b612-ce42c6096025',
                'Hélène',
                'MARCHOIS',
                new \DateTime('2024-05-14'),
            ),
        ];

        $guestRepository
            ->expects(self::once())
            ->method('findGuestsByEvent')
            ->with('37fa0f81-d9dd-4bbd-900b-9cc3b39d21e9', 20, 1)
            ->willReturn([
                'guests' => $guests,
                'count' => 2,
            ]);

        $query = new GetGuestsByEventQuery(
            '37fa0f81-d9dd-4bbd-900b-9cc3b39d21e9',
            1,
            20,
        );
        $handler = new GetGuestsByEventQueryHandler($guestRepository);

        $this->assertEquals(new Pagination($guests, 2, 1, 20), ($handler)($query));
    }
}
