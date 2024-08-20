<?php

namespace App\Repository;

use AllowDynamicProperties;
use App\Entity\Team;
use App\Service\PaginationService;
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
#[AllowDynamicProperties] class TeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, FollowRepository $followRepository, PaginationService $paginationService)
    {
        parent::__construct($registry, Team::class);
        $this->followRepository = $followRepository;
        $this->paginationService = $paginationService;
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
        $paginationParams = $this->paginationService->getPaginationParameters($request);
        $paginationParams['limit'] = 3;
        
        $teams = $this->findBy([], null, $paginationParams['limit'], $paginationParams['offset']);
        $totalTeams = $this->count([]);

        $followedTeamIds = $this->followRepository->getFollowedTeamIds($user);

        return [
            'teams' => $teams,
            'limit' => $paginationParams['limit'],
            'offset' => $paginationParams['offset'],
            'totalTeams' => $totalTeams,
            'followedTeamIds' => $followedTeamIds,
        ];
    }
}
