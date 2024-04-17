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

    public function findEventsByOwner(string $userUuid, int $pageSize, int $page): array
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
            ->where('e.owner = :userUuid')
            ->setParameter('userUuid', $userUuid)
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

    public function findOneByTitleAndOwner(string $title, string $userUuid): ?Event
    {
        return $this->createQueryBuilder('e')
            ->where('e.owner = :userUuid')
            ->andWhere('e.title = :title')
            ->setParameters([
                'userUuid' => $userUuid,
                'title' => $title,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByUuid(string $uuid): ?Event
    {
        return $this->createQueryBuilder('e')
            ->addSelect('o')
            ->where('e.uuid = :uuid')
            ->innerJoin('e.owner', 'o')
            ->setParameters([
                'uuid' => $uuid,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
