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
    
    public function getTeamStatistics(int $teamId): array
    {
        $results = $this->createQueryBuilder('sts')
            ->select('sts')
            ->where('sts.team = :teamId')
            ->setParameter('teamId', $teamId)
            ->getQuery()
            ->getResult();
        
        $statistics = [
            'position' => 0,
            'points' => 0,
            'played_games' => 0,
            'won' => 0,
            'draw' => 0,
            'lost' => 0,
            'goals_for' => 0,
            'goals_against' => 0,
            'goal_difference' => 0,
        ];
        
        foreach ($results as $result) {
            $statistics['position'] = $result->getPosition();
            $statistics['points'] = $result->getPoints();
            $statistics['played_games'] = $result->getPlayedGames();
            $statistics['won'] = $result->getWon();
            $statistics['draw'] = $result->getDraw();
            $statistics['lost'] = $result->getLost();
            $statistics['goals_for'] = $result->getGoalsFor();
            $statistics['goals_against'] = $result->getGoalsAgainst();
            $statistics['goal_difference'] = $result->getGoalDifference();
        }
        
        return $statistics;
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
