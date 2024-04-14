<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository\Participant;

use App\Application\Participant\View\ParticipantView;
use App\Domain\Participant\Participant;
use App\Domain\Participant\Repository\ParticipantRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

final class ParticipantRepository extends ServiceEntityRepository implements ParticipantRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Participant::class);
    }

    public function findParticipantsByEvent(string $userUuid, string $eventUuid, int $pageSize, int $page): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select(sprintf(
                'NEW %s(
                    p.uuid,
                    p.firstName,
                    p.lastName,
                    p.email,
                    p.accessCode,
                    p.accessSent
                )',
                ParticipantView::class,
            ))
            ->where('e.uuid = :eventUuid')
            ->andWhere('e.owner = :userUuid')
            ->innerJoin('p.event', 'e')
            ->orderBy('p.lastName', 'ASC')
            ->setParameters([
                'userUuid' => $userUuid,
                'eventUuid' => $eventUuid,
            ])
            ->setFirstResult($pageSize * ($page - 1))
            ->setMaxResults($pageSize);

        $query = $qb->getQuery();
        $paginator = new Paginator($query, false);
        $paginator->setUseOutputWalkers(false);

        $result = [
            'participants' => [],
            'count' => $paginator->count(),
        ];

        foreach ($paginator as $event) {
            array_push($result['participants'], $event);
        }

        return $result;
    }
}
