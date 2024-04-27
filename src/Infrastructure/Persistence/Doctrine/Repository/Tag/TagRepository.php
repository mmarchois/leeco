<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository\Tag;

use App\Application\Tag\View\TagView;
use App\Domain\Event\Event;
use App\Domain\Tag\Repository\TagRepositoryInterface;
use App\Domain\Tag\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

final class TagRepository extends ServiceEntityRepository implements TagRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Tag::class);
    }

    public function add(Tag $tag): Tag
    {
        $this->getEntityManager()->persist($tag);

        return $tag;
    }

    public function delete(Tag $tag): void
    {
        $this->getEntityManager()->remove($tag);
    }

    public function findTagsByEvent(string $eventUuid, int $pageSize, int $page): array
    {
        $qb = $this->createQueryBuilder('t')
            ->select(sprintf(
                'NEW %s(
                    t.uuid,
                    t.title,
                    t.startDate,
                    t.endDate
                )',
                TagView::class,
            ))
            ->where('e.uuid = :eventUuid')
            ->innerJoin('t.event', 'e')
            ->orderBy('t.startDate', 'ASC')
            ->addOrderBy('t.endDate', 'ASC')
            ->setParameter('eventUuid', $eventUuid)
            ->setFirstResult($pageSize * ($page - 1))
            ->setMaxResults($pageSize);

        $query = $qb->getQuery();
        $paginator = new Paginator($query, false);
        $paginator->setUseOutputWalkers(false);

        $result = [
            'tags' => [],
            'count' => $paginator->count(),
        ];

        foreach ($paginator as $event) {
            array_push($result['tags'], $event);
        }

        return $result;
    }

    public function findOneByEventAndTitle(Event $event, string $title): ?Tag
    {
        return $this->createQueryBuilder('t')
            ->where('t.event = :event')
            ->andWhere('t.title = :title')
            ->setParameters([
                'event' => $event,
                'title' => $title,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByUuid(string $uuid): ?Tag
    {
        return $this->createQueryBuilder('t')
            ->select('t')
            ->addSelect('e')
            ->where('t.uuid = :uuid')
            ->innerJoin('t.event', 'e')
            ->setParameter('uuid', $uuid)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
