<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Tag\Query;

use App\Application\Tag\Query\GetTagsByEventQuery;
use App\Application\Tag\Query\GetTagsByEventQueryHandler;
use App\Application\Tag\View\TagView;
use App\Domain\Pagination;
use App\Domain\Tag\Repository\TagRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class GetTagsByEventQueryHandlerTest extends TestCase
{
    public function testTags(): void
    {
        $tagRepository = $this->createMock(TagRepositoryInterface::class);
        $tags = [
            new TagView(
                '33795ae7-395a-440a-9fa8-f72343d62eb0',
                'Cérémonie religieuse',
                new \DateTime('2023-01-01 19:00:00'),
                new \DateTime('2023-01-01 21:00:00'),
            ),
            new TagView(
                'e8e165c3-64bf-46e5-b612-ce42c6096025',
                'Dîner',
                new \DateTime('2023-01-01 21:00:00'),
                new \DateTime('2023-01-01 23:00:00'),
            ),
        ];

        $tagRepository
            ->expects(self::once())
            ->method('findTagsByEvent')
            ->with('37fa0f81-d9dd-4bbd-900b-9cc3b39d21e9', 20, 1)
            ->willReturn([
                'tags' => $tags,
                'count' => 2,
            ]);

        $query = new GetTagsByEventQuery(
            '37fa0f81-d9dd-4bbd-900b-9cc3b39d21e9',
            1,
            20,
        );
        $handler = new GetTagsByEventQueryHandler($tagRepository);

        $this->assertEquals(new Pagination($tags, 2, 1, 20), ($handler)($query));
    }
}
