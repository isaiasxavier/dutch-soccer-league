<?php

namespace App\Repository;

use App\Entity\RunningCompetition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RunningCompetition>
 *
 * @method RunningCompetition|null find($id, $lockMode = null, $lockVersion = null)
 * @method RunningCompetition|null findOneBy(array $criteria, array $orderBy = null)
 * @method RunningCompetition[]    findAll()
 * @method RunningCompetition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RunningCompetitionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RunningCompetition::class);
    }

    //    /**
    //     * @return RunningCompetition[] Returns an array of RunningCompetition objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?RunningCompetition
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
