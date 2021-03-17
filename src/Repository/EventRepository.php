<?php

namespace App\Repository;

use App\Classes\Filters;
use App\Entity\Campus;
use App\Entity\Event;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;

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

    public function filterArchive(DateTimeInterface $dateArchive)
    {
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.date >= :date')
            ->setParameter('date', $dateArchive);
        $query = $qb->getQuery();
        return $query->execute();
    }

    public function filterMobile(DateTimeInterface $dateArchive, Campus $campus)
    {
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.date >= :date')
            ->andWhere('e.campus = :campus')
            ->setParameters(new ArrayCollection([
                new Parameter('date', $dateArchive),
                new Parameter('campus', $campus)
            ]));
        $query = $qb->getQuery();
        return $query->execute();
    }

    public function mainSearch(DateTimeInterface $dateArchive, Filters $filters)
    {
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.date >= :date')
            ->setParameter('date', $dateArchive);
        if ($filters->getCampus()) {
            $qb->andWhere('e.campus = :campus')
                ->setParameter('campus', $filters->getCampus());
        }
        if ($filters->getEvent()) {
            $qb->andWhere('e.name like :event')
                ->setParameter('event', '%' . $filters->getEvent() . '%');
        }
        if ($filters->getDateFrom()) {
            $qb->andWhere('e.date >= :dateFrom')
                ->setParameter('dateFrom', $filters->getDateFrom());
        }
        if ($filters->getDateTo()) {
            $qb->andWhere('e.limitDate <= :dateTo')
                ->setParameter('dateTo', $filters->getDateTo());
        }
        if ($filters->getUser()) {
            $qb->andWhere('e.user = :user')
                ->setParameter('user', $filters->getUser());
        }
        if ($filters->getUserSub()) {
            $qb->andWhere('e.name in (:userSub)')
                ->setParameter('userSub', $filters->getEventNames());
        }
        if ($filters->getUserNonsub()) {
            $qb->andWhere($qb->expr()->notIn('e.name', ':userSub'))
                ->setParameter('userSub', $filters->getEventNames());
        }
        if ($filters->getPast()) {
            $qb->andWhere('e.status = :status')
                ->setParameter('status', $filters->getStatus());
        }
        $query = $qb->getQuery();
        return $query->execute();
    }
}
