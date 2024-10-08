<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Repository\CoachRepository;
use App\Repository\FollowRepository;
use App\Repository\GameMatchRepository;
use App\Repository\PlayerRepository;
use App\Repository\SeasonTeamStandingRepository;
use App\Repository\TeamRepository;
use App\Service\PaginationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[AllowDynamicProperties]
class TeamController extends AbstractController
{
    private SeasonTeamStandingRepository $seasonTeamStandingRepository;
    
    public function __construct(
        TeamRepository $teamRepository,
        CoachRepository $coachRepository,
        PlayerRepository $playerRepository,
        GameMatchRepository $gameMatchRepository,
        FollowRepository $followRepository,
        PaginationService $paginationService,
        SeasonTeamStandingRepository $seasonTeamStandingRepository,
    ) {
        $this->teamRepository = $teamRepository;
        $this->coachRepository = $coachRepository;
        $this->playerRepository = $playerRepository;
        $this->gameMatchRepository = $gameMatchRepository;
        $this->followRepository = $followRepository;
        $this->paginationService = $paginationService;
        $this->seasonTeamStandingRepository = $seasonTeamStandingRepository;
    }

    #[Route('/teams/{id}', name: 'app_team_detail')]
    public function teamDetail($id, Request $request): Response
    {
        $team = $this->teamRepository->findTeamById($id);
        $coach = $this->coachRepository->findCoachByTeamId($id);
        $players = $this->playerRepository->findPlayerByTeamId($id);
        $pagination = $this->paginationService->getPaginationParameters($request);
        
        $team_id = $team->getId();
        $statistics = $this->seasonTeamStandingRepository->getTeamStatistics($team_id);

        $totalMatches = $this->gameMatchRepository->countMatchesByTeamId($team_id);
        $matches = $this->gameMatchRepository->findMatchesByTeamId($team_id, $pagination['limit'], $pagination['offset']);

        return $this->render('team/detail.html.twig', [
            'team' => $team,
            'coach' => $coach,
            'squad' => $players,
            'matches' => $matches,
            'limit' => $pagination['limit'],
            'offset' => $pagination['offset'],
            'total_matches' => $totalMatches,
            'statistics' => $statistics,
        ]);
    }
}
