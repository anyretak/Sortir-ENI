<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function filterArchive($value1)
    {
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.date >= :date')
            ->setParameter('date', $value1);

        $query = $qb->getQuery();
        return $query->execute();
    }

    public function mainSearch($value1, $value2, $value3, $value4, $value5, $value6, $value7, $value8, $value9, $value10)
    {
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.date >= :date')
            ->setParameter('date', $value1);

        if ($value2) {
            $qb->andWhere('e.campus = :campus')
                ->setParameter('campus', $value2);
        }
        if ($value3 !== '') {
            $qb->andWhere('e.name = :event')
                ->setParameter('event', $value3);
        }
        if ($value4) {
            $qb->andWhere('e.date >= :dateFrom')
                ->setParameter('dateFrom', $value4);
        }
        if ($value5) {
            $qb->andWhere('e.limitDate <= :dateTo')
                ->setParameter('dateTo', $value5);
        }
        if ($value6) {
            $qb->andWhere('e.user = :user')
                ->setParameter('user', $value6);
        }
        if ($value7) {
            $qb->andWhere('e.name in (:userSub)')
               ->setParameter('userSub', $value9);
        }
        if ($value8) {
            $qb->andWhere($qb->expr()->notIn('e.name', ':userSub'))
                ->setParameter('userSub', $value9);
        }
        if ($value10) {
            $qb->andWhere('e.status = :status')
                ->setParameter('status', $value10);
        }

        $query = $qb->getQuery();
        return $query->execute();
    }
}
