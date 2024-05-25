<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository\Media;

use App\Domain\Media\Media;
use App\Domain\Media\Repository\MediaRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
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

    public function findGuestMediasByEvent(string $eventUuid, int $pageSize, int $page): array
    {
        $qb = $this->createQueryBuilder('m')
            ->select('m.uuid, g.firstName, g.lastName, m.path')
            ->where('m.event = :eventUuid')
            ->andWhere('m.guest IS NOT NULL')
            ->innerJoin('m.guest', 'g')
            ->addOrderBy('m.createdAt', 'DESC')
            ->setParameters([
                'eventUuid' => $eventUuid,
            ])
            ->setFirstResult($pageSize * ($page - 1))
            ->setMaxResults($pageSize);

        $query = $qb->getQuery();
        $paginator = new Paginator($query, false);
        $paginator->setUseOutputWalkers(false);

        return [
            'count' => $paginator->count(),
            'results' => $paginator,
        ];
    }
}
