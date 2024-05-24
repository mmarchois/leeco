<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository\Media;

use App\Domain\Media\Media;
use App\Domain\Media\Repository\MediaRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class MediaRepository extends ServiceEntityRepository implements MediaRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Media::class);
    }

    public function add(Media $media): Media
    {
        $this->getEntityManager()->persist($media);

        return $media;
    }
}
