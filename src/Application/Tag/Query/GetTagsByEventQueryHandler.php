<?php

declare(strict_types=1);

namespace App\Application\Tag\Query;

use App\Domain\Pagination;
use App\Domain\Tag\Repository\TagRepositoryInterface;

final class GetTagsByEventQueryHandler
{
    public function __construct(
        private TagRepositoryInterface $tagRepository,
    ) {
    }

    public function __invoke(GetTagsByEventQuery $query): Pagination
    {
        ['tags' => $tags, 'count' => $count] = $this->tagRepository->findTagsByEvent(
            $query->eventUuid,
            $query->pageSize,
            $query->page,
        );

        return new Pagination($tags, $count, $query->page, $query->pageSize);
    }
}
