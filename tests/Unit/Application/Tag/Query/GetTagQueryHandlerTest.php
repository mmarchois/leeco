<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Tag\Query;

use App\Application\Tag\Query\GetTagQuery;
use App\Application\Tag\Query\GetTagQueryHandler;
use App\Domain\Event\Event;
use App\Domain\Tag\Exception\TagNotBelongsToEventException;
use App\Domain\Tag\Exception\TagNotFoundException;
use App\Domain\Tag\Repository\TagRepositoryInterface;
use App\Domain\Tag\Tag;
use PHPUnit\Framework\TestCase;

final class GetTagQueryHandlerTest extends TestCase
{
    public function testGetTag(): void
    {
        $tagRepository = $this->createMock(TagRepositoryInterface::class);
        $event = $this->createMock(Event::class);
        $tag = $this->createMock(Tag::class);
        $tag
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($event);

        $tagRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->with('37fa0f81-d9dd-4bbd-900b-9cc3b39d21e9')
            ->willReturn($tag);

        $query = new GetTagQuery(
            $event,
            '37fa0f81-d9dd-4bbd-900b-9cc3b39d21e9',
        );
        $handler = new GetTagQueryHandler($tagRepository);

        $this->assertEquals($tag, ($handler)($query));
    }

    public function testTagNotBelonsToEvent(): void
    {
        $this->expectException(TagNotBelongsToEventException::class);

        $tagRepository = $this->createMock(TagRepositoryInterface::class);
        $event = $this->createMock(Event::class);
        $event2 = $this->createMock(Event::class);

        $tag = $this->createMock(Tag::class);
        $tag
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($event2);

        $tagRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->with('37fa0f81-d9dd-4bbd-900b-9cc3b39d21e9')
            ->willReturn($tag);

        $query = new GetTagQuery(
            $event,
            '37fa0f81-d9dd-4bbd-900b-9cc3b39d21e9',
        );
        $handler = new GetTagQueryHandler($tagRepository);

        ($handler)($query);
    }

    public function testTagNotFound(): void
    {
        $this->expectException(TagNotFoundException::class);

        $tagRepository = $this->createMock(TagRepositoryInterface::class);
        $event = $this->createMock(Event::class);

        $tagRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->with('37fa0f81-d9dd-4bbd-900b-9cc3b39d21e9')
            ->willReturn(null);

        $query = new GetTagQuery(
            $event,
            '37fa0f81-d9dd-4bbd-900b-9cc3b39d21e9',
        );
        $handler = new GetTagQueryHandler($tagRepository);

        ($handler)($query);
    }
}
