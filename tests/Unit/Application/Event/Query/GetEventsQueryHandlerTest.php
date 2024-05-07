<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Event\Query;

use App\Application\Event\Query\GetEventsQuery;
use App\Application\Event\Query\GetEventsQueryHandler;
use App\Application\Event\View\EventView;
use App\Domain\Event\Repository\EventRepositoryInterface;
use App\Domain\Pagination;
use PHPUnit\Framework\TestCase;

final class GetEventsQueryHandlerTest extends TestCase
{
    public function testGetSummarizedEvents(): void
    {
        $eventRepository = $this->createMock(EventRepositoryInterface::class);
        $events = [
            new EventView('33795ae7-395a-440a-9fa8-f72343d62eb0', 'Mariage H&M', new \DateTime('2019-01-05'), new \DateTime('2019-01-07')),
            new EventView('a273bde2-d872-4eec-973e-fcc23adfef75', 'Baptême Raphaël', new \DateTime('2020-01-01'), new \DateTime('2020-01-01')),
            new EventView('c2ebef0c-77e8-4586-aa5f-ae6faccdb73e', 'Baptême Baptiste', new \DateTime('2022-01-01'), new \DateTime('2022-01-01')),
        ];

        $eventRepository
            ->expects(self::once())
            ->method('findEventsByOwner')
            ->with('37fa0f81-d9dd-4bbd-900b-9cc3b39d21e9', 20, 1)
            ->willReturn([
                'events' => $events,
                'count' => 2,
            ]);

        $query = new GetEventsQuery('37fa0f81-d9dd-4bbd-900b-9cc3b39d21e9', 1, 20);
        $handler = new GetEventsQueryHandler($eventRepository);

        $this->assertEquals(new Pagination($events, 2, 1, 20), ($handler)($query));
    }
}
