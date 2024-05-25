<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Media\Query;

use App\Application\Media\Query\GetMediasByEventQuery;
use App\Application\Media\Query\GetMediasByEventQueryHandler;
use App\Application\Media\View\MediaView;
use App\Domain\Media\Repository\MediaRepositoryInterface;
use App\Domain\Pagination;
use PHPUnit\Framework\TestCase;

final class GetMediasByEventQueryHandlerTest extends TestCase
{
    public function testGetMedias(): void
    {
        $mediaRepository = $this->createMock(MediaRepositoryInterface::class);
        $medias = [
            new MediaView(
                uuid: '33795ae7-395a-440a-9fa8-f72343d62eb0',
                path: 'https://s3.url/path/1.jpg',
                author: 'Mathieu MARCHOIS',
            ),
            new MediaView(
                uuid: 'a273bde2-d872-4eec-973e-fcc23adfef75',
                path: 'https://s3.url/path/2.jpg',
                author: 'Hélène MARCHOIS',
            ),
        ];

        $mediaRepository
            ->expects(self::once())
            ->method('findGuestMediasByEvent')
            ->with('37fa0f81-d9dd-4bbd-900b-9cc3b39d21e9', 20, 1)
            ->willReturn([
                'results' => [
                    [
                        'uuid' => '33795ae7-395a-440a-9fa8-f72343d62eb0',
                        'path' => 'path/1.jpg',
                        'firstName' => 'Mathieu',
                        'lastName' => 'MARCHOIS',
                    ],
                    [
                        'uuid' => 'a273bde2-d872-4eec-973e-fcc23adfef75',
                        'path' => 'path/2.jpg',
                        'firstName' => 'Hélène',
                        'lastName' => 'MARCHOIS',
                    ],
                ],
                'count' => 2,
            ]);

        $query = new GetMediasByEventQuery('37fa0f81-d9dd-4bbd-900b-9cc3b39d21e9', 1, 20);
        $handler = new GetMediasByEventQueryHandler($mediaRepository, 'https://s3.url');

        $this->assertEquals(new Pagination($medias, 2, 1, 20), ($handler)($query));
    }
}
