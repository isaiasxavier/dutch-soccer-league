<?php

namespace App\Repository;

use App\Entity\Standing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Standing>
 *
 * @method Standing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Standing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Standing[]    findAll()
 * @method Standing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StandingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Standing::class);
    }

    public function findBySeason($season): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.season = :season')
            ->setParameter('season', $season)
            ->getQuery()
            ->getResult();
    }
}
