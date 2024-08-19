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
    
    public function findByStandings(array $standings): array
    {
        return $this->createQueryBuilder('sts')
            ->where('sts.standing IN (:standings)')
            ->setParameter('standings', $standings)
            ->getQuery()
            ->getResult();
    }
}
