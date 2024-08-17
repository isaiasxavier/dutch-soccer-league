<?php

namespace App\Repository;

use App\Entity\GameMatch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameMatch>
 *
 * @method GameMatch|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameMatch|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameMatch[]    findAll()
 * @method GameMatch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameMatchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameMatch::class);
    }

    //    /**
    //     * @return GameMatchBKUP[] Returns an array of GameMatchBKUP objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('g.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?GameMatchBKUP
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
