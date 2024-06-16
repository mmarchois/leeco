<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository\Guest;

use App\Application\Guest\View\GuestView;
use App\Domain\Guest\Guest;
use App\Domain\Guest\Repository\GuestRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

final class GuestRepository extends ServiceEntityRepository implements GuestRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Guest::class);
    }

    public function add(Guest $guest): Guest
    {
        $this->getEntityManager()->persist($guest);

        return $guest;
    }

    public function delete(Guest $guest): void
    {
        $this->getEntityManager()->remove($guest);
    }

    public function findGuestsByEvent(string $eventUuid, int $pageSize, int $page): array
    {
        $qb = $this->createQueryBuilder('g')
            ->select(sprintf(
                'NEW %s(
                    g.uuid,
                    g.firstName,
                    g.lastName,
                    g.createdAt
                )',
                GuestView::class,
            ))
            ->where('e.uuid = :eventUuid')
            ->innerJoin('g.event', 'e')
            ->orderBy('g.createdAt', 'DESC')
            ->setParameters([
                'eventUuid' => $eventUuid,
            ])
            ->setFirstResult($pageSize * ($page - 1))
            ->setMaxResults($pageSize);

        $query = $qb->getQuery();
        $paginator = new Paginator($query, false);
        $paginator->setUseOutputWalkers(false);

        $result = [
            'guests' => [],
            'count' => $paginator->count(),
        ];

        foreach ($paginator as $event) {
            array_push($result['guests'], $event);
        }

        return $result;
    }

    public function findOneByUuid(string $uuid): ?Guest
    {
        return $this->createQueryBuilder('g')
            ->select('g')
            ->addSelect('e')
            ->where('g.uuid = :uuid')
            ->innerJoin('g.event', 'e')
            ->setParameter('uuid', $uuid)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByDeviceIdentifierAndEvent(string $deviceIdentifier, string $eventUuid): ?Guest
    {
        return $this->createQueryBuilder('g')
            ->where('e.uuid = :eventUuid')
            ->andWhere('g.deviceIdentifier = :deviceIdentifier')
            ->innerJoin('g.event', 'e')
            ->setParameters([
                'eventUuid' => $eventUuid,
                'deviceIdentifier' => $deviceIdentifier,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
