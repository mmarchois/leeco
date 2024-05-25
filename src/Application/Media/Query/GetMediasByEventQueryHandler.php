<?php

declare(strict_types=1);

namespace App\Application\Media\Query;

use App\Application\Media\View\MediaView;
use App\Domain\Media\Repository\MediaRepositoryInterface;
use App\Domain\Pagination;

final class GetMediasByEventQueryHandler
{
    public function __construct(
        private MediaRepositoryInterface $mediaRepository,
        private string $awsPublicUrl,
    ) {
    }

    public function __invoke(GetMediasByEventQuery $query): Pagination
    {
        ['results' => $results, 'count' => $count] = $this->mediaRepository->findGuestMediasByEvent(
            $query->eventUuid,
            $query->pageSize,
            $query->page,
        );

        $medias = [];

        foreach ($results as $media) {
            $medias[] = new MediaView(
                uuid: $media['uuid'],
                path: sprintf('%s/%s', $this->awsPublicUrl, $media['path']),
                author: sprintf('%s %s', $media['firstName'], $media['lastName']),
            );
        }

        return new Pagination($medias, $count, $query->page, $query->pageSize);
    }
}
