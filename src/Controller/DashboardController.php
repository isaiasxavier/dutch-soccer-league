<?php

namespace App\Controller;

use App\Entity\Follow;
use App\Repository\TeamRepository;
use Doctrine\Persistence\ManagerRegistry;
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
    public function dashboard(Request $request, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        
        // Definindo limite e offset para a paginação
        $limit = $request->query->getInt('limit', 3);
        $offset = $request->query->getInt('offset', 0);
        
        // Obtendo os times com paginação
        $teams = $this->teamRepository->findBy([], null, $limit, $offset);
        
        // Carregando todos os times seguidos pelo usuário
        $followedTeams = $doctrine->getRepository(Follow::class)
            ->findBy(['user' => $user]);
        
        // Criando um array com os IDs dos times seguidos pelo usuário
        $followedTeamIds = [];
        foreach ($followedTeams as $follow) {
            $followedTeamIds[] = $follow->getTeam()->getId();
        }
        
        // Renderizando o template e passando as variáveis necessárias
        return $this->render('dashboard/dashboard.html.twig', [
            'teams' => $teams,
            'limit' => $limit,
            'offset' => $offset,
            'followed_team_ids' => $followedTeamIds, // Passa a lista de IDs dos times seguidos
        ]);
    }
    
}
