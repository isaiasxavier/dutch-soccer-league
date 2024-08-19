<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Repository\CoachRepository;
use App\Repository\FollowRepository;
use App\Repository\GameMatchRepository;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[AllowDynamicProperties] class TeamController extends AbstractController
{
    public function __construct(
        TeamRepository $teamRepository,
        CoachRepository $coachRepository,
        PlayerRepository $playerRepository,
        GameMatchRepository $gameMatchRepository,
        FollowRepository $followRepository,
    ) {
        $this->teamRepository = $teamRepository;
        $this->coachRepository = $coachRepository;
        $this->playerRepository = $playerRepository;
        $this->gameMatchRepository = $gameMatchRepository;
        $this->followRepository = $followRepository;
    }

    #[Route('/teams/{id}', name: 'app_team_detail')]
    public function teamDetail($id, Request $request): Response
    {
        $team = $this->teamRepository->find($id);
        $coach = $this->coachRepository->findOneBy(['team' => $team]);
        $players = $this->playerRepository->findBy(['team' => $team]);
        $limit = $request->query->getInt('limit', 25);
        $offset = $request->query->getInt('offset', 0);

        $matches = $this->gameMatchRepository->createQueryBuilder('gm')
            ->where('gm.homeTeamId = :teamId OR gm.awayTeamId = :teamId')
            ->setParameter('teamId', $id)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return $this->render('team/detail.html.twig', [
            'team' => $team,
            'coach' => $coach,
            'squad' => $players,
            'matches' => $matches,
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }
}
