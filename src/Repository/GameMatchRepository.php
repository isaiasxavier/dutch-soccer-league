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

    public function countMatchesByTeamId($teamId)
    {
        return $this->createQueryBuilder('gm')
            ->select('count(gm.id)')
            ->where('gm.homeTeamId = :teamId OR gm.awayTeamId = :teamId')
            ->setParameter('teamId', $teamId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findMatchesByTeamId($teamId, $limit, $offset)
    {
        return $this->createQueryBuilder('gm')
            ->where('gm.homeTeamId = :teamId OR gm.awayTeamId = :teamId')
            ->setParameter('teamId', $teamId)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
