<?php

namespace App\Repository;

use App\Entity\Task;
use Datetime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @param Datetime $date1
     * @param Datetime $date2
     * @return int|mixed|string
     * @throws Exception
     */
    public function findByDate(Datetime $date1, Datetime $date2)
    {
        $qb = $this->createQueryBuilder("e");
        $qb
            ->andWhere('e.date BETWEEN :from AND :to')
            ->setParameter('from', $date1 )
            ->setParameter('to', $date2)
        ;

        $result = $qb->getQuery()->getResult();

        return $result;
    }
}
