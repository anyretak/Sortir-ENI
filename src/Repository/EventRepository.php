<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
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

    public function findByMinDate($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.date >= :val')
            ->setParameter('val', $value)
            /*            ->orderBy('e.id', 'ASC')
                        ->setMaxResults(10)*/
            ->getQuery()
            ->getResult();
    }

    public function findByMaxDate($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.date <= :val')
            ->setParameter('val', $value)
            /*            ->orderBy('e.id', 'ASC')
                        ->setMaxResults(10)*/
            ->getQuery()
            ->getResult();
    }

    public function findByDate($value1, $value2)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.date >= ?1')
            ->andWhere('e.date <= ?2')
            ->setParameters(new ArrayCollection([
                new Parameter('1', $value1),
                new Parameter('2', $value2)
            ]))
            ->getQuery()
            ->getResult();
    }
}
