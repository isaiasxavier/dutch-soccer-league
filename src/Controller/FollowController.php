<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Entity\Follow;
use App\Repository\FollowRepository;
use App\Repository\SeasonTeamStandingRepository;
use App\Repository\StandingRepository;
use App\Repository\TeamRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[AllowDynamicProperties] class FollowController extends AbstractController
{
    public function __construct(
        TeamRepository $teamRepository,
        FollowRepository $followRepository,
        SeasonTeamStandingRepository $seasonTeamStandingRepository,
        StandingRepository $standingRepository,
    ) {
        $this->teamRepository = $teamRepository;
        $this->followRepository = $followRepository;
        $this->seasonTeamStandingRepository = $seasonTeamStandingRepository;
        $this->standingRepository = $standingRepository;}
    
    #[Route('/follow', name: 'app_follow')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        // Carrega todos os times seguidos pelo usuário
        $followedTeams = $doctrine->getRepository(Follow::class)
            ->findBy(['user' => $user]);
        
        $teams = [];
        foreach ($followedTeams as $follow) {
            $teams[] = $follow->getTeam();
        }
        
        return $this->render('follow/follow.html.twig', [
            'followed_teams' => $teams,
        ]);
    }
    
    
    
}
