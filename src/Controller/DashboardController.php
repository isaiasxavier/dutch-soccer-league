<?php

namespace App\Controller;

use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    private TeamRepository $teamRepository;
    
    public function __construct(TeamRepository $teamRepository)
    {
        $this->teamRepository = $teamRepository;
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
