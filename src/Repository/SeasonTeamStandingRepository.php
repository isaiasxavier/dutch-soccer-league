<?php

namespace App\Repository;

use App\Entity\SeasonTeamStanding;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SeasonTeamStanding>
 *
 * @method SeasonTeamStanding|null find($id, $lockMode = null, $lockVersion = null)
 * @method SeasonTeamStanding|null findOneBy(array $criteria, array $orderBy = null)
 * @method SeasonTeamStanding[]    findAll()
 * @method SeasonTeamStanding[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeasonTeamStandingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SeasonTeamStanding::class);
    }

    //    /**
    //     * @return SeasonTeamStanding[] Returns an array of SeasonTeamStanding objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?SeasonTeamStanding
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
