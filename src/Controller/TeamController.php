<?php

namespace App\Controller;

use App\Repository\CoachRepository;
use App\Repository\GameMatchRepository;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[\AllowDynamicProperties] class TeamController extends AbstractController
{
    /*private $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    #[Route('/teams', name: 'app_teams')]
    public function listTeams(Request $request): Response
    {
        $limit = $request->query->getInt('limit', 10);
        $offset = $request->query->getInt('offset', 0);
        $teams = $this->apiService->getTeams($limit, $offset);

        $this->logger->info('Teams Data', ['teams' => $teams]);

        return $this->render('team/list.html.twig', [
            'teams' => $teams,
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }*/

    public function __construct(
        TeamRepository $teamRepository,
        CoachRepository $coachRepository,
        PlayerRepository $playerRepository,
        GameMatchRepository $gameMatchRepository,
    ) {
        $this->teamRepository = $teamRepository;
        $this->coachRepository = $coachRepository;
        $this->playerRepository = $playerRepository;
        $this->gameMatchRepository = $gameMatchRepository;
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

    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard(Request $request): Response
    {
        /*$limit = $request->query->getInt('limit', 10);
        $offset = $request->query->getInt('offset', 0);
        $teams = $this->apiService->getTeams($limit, $offset);

        return $this->render('dashboard/dashboard.html.twig', [
            'teams' => $teams,
            'limit' => $limit,
            'offset' => $offset,
        ]);*/
        $limit = $request->query->getInt('limit', 3);
        $offset = $request->query->getInt('offset', 0);
        $teams = $this->teamRepository->findBy([], null, $limit, $offset);

        return $this->render('dashboard/dashboard.html.twig', [
            'teams' => $teams,
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }
}
