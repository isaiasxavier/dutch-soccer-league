<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Entity\Follow;
use App\Repository\TeamRepository;
use Doctrine\Persistence\ManagerRegistry;
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
    public function dashboard(Request $request, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();

        $limit = $request->query->getInt('limit', 3);
        $offset = $request->query->getInt('offset', 0);

        // Obtendo os times com paginação
        $teams = $this->teamRepository->findBy([], null, $limit, $offset);
        
        // Contando o número total de times
        $totalTeams = $this->teamRepository->count([]);

        $followedTeams = $doctrine->getRepository(Follow::class)
            ->findBy(['user' => $user]);

        $followedTeamIds = [];
        foreach ($followedTeams as $follow) {
            $followedTeamIds[] = $follow->getTeam()->getId();
        }

        return $this->render('dashboard/dashboard.html.twig', [
            'teams' => $teams,
            'limit' => $limit,
            'offset' => $offset,
            'total_teams' => $totalTeams,
            'followed_team_ids' => $followedTeamIds,
        ]);
    }
}
