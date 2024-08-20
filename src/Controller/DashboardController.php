<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[AllowDynamicProperties] class DashboardController extends AbstractController
{
    public function __construct(TeamRepository $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard(Request $request): Response
    {
        $user = $this->getUser();

        $teamsData = $this->teamRepository->findTeamsWithPaginationAndCountAndFollowers($request, $user);

        return $this->render('dashboard/dashboard.html.twig', [
            'teams' => $teamsData['teams'],
            'limit' => $teamsData['limit'],
            'offset' => $teamsData['offset'],
            'total_teams' => $teamsData['totalTeams'],
            'followed_team_ids' => $teamsData['followedTeamIds'],
        ]);
    }
}
