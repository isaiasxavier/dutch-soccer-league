<?php

namespace App\Controller;

use App\Service\ApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeamController extends AbstractController
{
    private $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
       
    }

    /*#[Route('/teams', name: 'app_teams')]
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

    #[Route('/teams/{id}', name: 'app_team_detail')]
    public function teamDetail($id, Request $request): Response
    {
        $team = $this->apiService->getTeamById($id);
        $limit = $request->query->getInt('limit', 25);
        $offset = $request->query->getInt('offset', 0);
        $matches = $this->apiService->getMatchesByTeam($id, $limit, $offset);

        return $this->render('team/detail.html.twig', [
            'team' => $team,
            'matches' => $matches['matches'],
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }

    #[Route('/homepage', name: 'app_homepage')]
    public function homepage(Request $request): Response
    {
        $limit = $request->query->getInt('limit', 10);
        $offset = $request->query->getInt('offset', 0);
        $teams = $this->apiService->getTeams($limit, $offset);
        
        return $this->render('homepage/index.html.twig', [
            'teams' => $teams,
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }
}
