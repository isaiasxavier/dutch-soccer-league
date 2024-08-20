<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Repository\CoachRepository;
use App\Repository\FollowRepository;
use App\Repository\GameMatchRepository;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use App\Service\PaginationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[AllowDynamicProperties]
class TeamController extends AbstractController
{
    public function __construct(
        TeamRepository $teamRepository,
        CoachRepository $coachRepository,
        PlayerRepository $playerRepository,
        GameMatchRepository $gameMatchRepository,
        FollowRepository $followRepository,
        PaginationService $paginationService,
    ) {
        $this->teamRepository = $teamRepository;
        $this->coachRepository = $coachRepository;
        $this->playerRepository = $playerRepository;
        $this->gameMatchRepository = $gameMatchRepository;
        $this->followRepository = $followRepository;
        $this->paginationService = $paginationService;
    }

    #[Route('/teams/{id}', name: 'app_team_detail')]
    public function teamDetail($id, Request $request): Response
    {
        $team = $this->teamRepository->findTeamById($id);
        $coach = $this->coachRepository->findCoachByTeamId($id);
        $players = $this->playerRepository->findPlayerByTeamId($id);
        $pagination = $this->paginationService->getPaginationParameters($request);

        $totalMatches = $this->gameMatchRepository->countMatchesByTeamId($id);
        $matches = $this->gameMatchRepository->findMatchesByTeamId($id, $pagination['limit'], $pagination['offset']);

        return $this->render('team/detail.html.twig', [
            'team' => $team,
            'coach' => $coach,
            'squad' => $players,
            'matches' => $matches,
            'limit' => $pagination['limit'],
            'offset' => $pagination['offset'],
            'total_matches' => $totalMatches,
        ]);
    }
}
