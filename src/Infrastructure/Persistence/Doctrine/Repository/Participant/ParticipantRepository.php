<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository\Participant;

use App\Application\Participant\View\ParticipantView;
use App\Domain\Event\Event;
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

    public function add(Participant $participant): Participant
    {
        $this->getEntityManager()->persist($participant);

        return $participant;
    }

    public function delete(Participant $participant): void
    {
        $this->getEntityManager()->remove($participant);
    }

    public function findParticipantsByEvent(string $eventUuid, int $pageSize, int $page): array
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
            ->innerJoin('p.event', 'e')
            ->orderBy('p.lastName', 'ASC')
            ->setParameters([
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

    public function findOneByEventAndEmail(Event $event, string $email): ?Participant
    {
        return $this->createQueryBuilder('p')
            ->where('p.email = :email')
            ->andWhere('p.event = :event')
            ->setParameters([
                'email' => $email,
                'event' => $event,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByUuid(string $uuid): ?Participant
    {
        return $this->createQueryBuilder('p')
            ->select('p')
            ->addSelect('e')
            ->where('p.uuid = :uuid')
            ->innerJoin('p.event', 'e')
            ->setParameter('uuid', $uuid)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
