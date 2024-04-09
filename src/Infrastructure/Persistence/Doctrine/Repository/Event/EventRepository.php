<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository\Event;

use App\Application\Event\View\SummarizedEventView;
use App\Domain\Event\Event;
use App\Domain\Event\Repository\EventRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

final class EventRepository extends ServiceEntityRepository implements EventRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Event::class);
    }

    public function findEventsByOwner(string $ownerUuid, int $pageSize, int $page): array
    {
        $qb = $this->createQueryBuilder('e')
            ->select(sprintf(
                'NEW %s(
                    e.uuid,
                    e.title,
                    e.date
                )',
                SummarizedEventView::class,
            ))
            ->orderBy('e.date', 'DESC')
            ->where('e.owner = :ownerUuid')
            ->setParameter('ownerUuid', $ownerUuid)
            ->setFirstResult($pageSize * ($page - 1))
            ->setMaxResults($pageSize);

        $query = $qb->getQuery();
        $paginator = new Paginator($query, false);
        $paginator->setUseOutputWalkers(false);

        $result = [
            'events' => [],
            'count' => $paginator->count(),
        ];

        foreach ($paginator as $event) {
            array_push($result['events'], $event);
        }

        return $result;
    }
}