<?php

namespace App\Repository;

use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends ServiceEntityRepository<Team>
 *
 * @method Team|null find($id, $lockMode = null, $lockVersion = null)
 * @method Team|null findOneBy(array $criteria, array $orderBy = null)
 * @method Team[]    findAll()
 * @method Team[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
#[\AllowDynamicProperties] class TeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, FollowRepository $followRepository)
    {
        parent::__construct($registry, Team::class);
        $this->followRepository = $followRepository;
    }

    public function findTeamById($id): ?Team
    {
        return $this->find($id);
    }

    public function findTeamsByIds(array $ids): array
    {
        return $this->findBy(['id' => $ids]);
    }

    public function findTeamsWithPaginationAndCountAndFollowers(Request $request, $user): array
    {
        $limit = $request->query->getInt('limit', 3);
        $offset = $request->query->getInt('offset', 0);

        $teams = $this->findBy([], null, $limit, $offset);
        $totalTeams = $this->count([]);

        $followedTeamIds = $this->followRepository->getFollowedTeamIds($user);

        return [
            'teams' => $teams,
            'limit' => $limit,
            'offset' => $offset,
            'totalTeams' => $totalTeams,
            'followedTeamIds' => $followedTeamIds,
        ];
    }
}
