<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository\Event;

use App\Application\Event\View\EventView;
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

    public function add(Event $event): Event
    {
        $this->getEntityManager()->persist($event);

        return $event;
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
                EventView::class,
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

    public function findOneByTitleAndOwner(string $title, string $ownerUuid): ?Event
    {
        return $this->createQueryBuilder('e')
            ->where('e.owner = :ownerUuid')
            ->andWhere('e.title = :title')
            ->setParameters([
                'ownerUuid' => $ownerUuid,
                'title' => $title,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByUuidAndOwner(string $uuid, string $ownerUuid): ?EventView
    {
        return $this->createQueryBuilder('e')
            ->select(sprintf(
                'NEW %s(
                    e.uuid,
                    e.title,
                    e.date
                )',
                EventView::class,
            ))
            ->where('e.uuid = :uuid')
            ->andWhere('e.owner = :ownerUuid')
            ->setParameters([
                'uuid' => $uuid,
                'ownerUuid' => $ownerUuid,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByUuid(string $uuid): ?Event
    {
        return $this->createQueryBuilder('e')
            ->where('e.uuid = :uuid')
            ->setParameters([
                'uuid' => $uuid,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
