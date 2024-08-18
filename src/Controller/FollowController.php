<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Entity\Follow;
use App\Repository\FollowRepository;
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
    ) {
        $this->teamRepository = $teamRepository;
        $this->followRepository = $followRepository;
    }
    #[Route('/follow', name: 'app_follow')]
    public function index(): Response
    {
        return $this->render('follow/index.html.twig', [
            'controller_name' => 'FollowController',
        ]);
    }
    
    #[Route('/teams/{id}/follow', name: 'app_follow_list')]
    public function followTeam($id, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        $team = $this->teamRepository->find($id);
        
        if ($user && $team) {
            $followRepository = $doctrine->getRepository(Follow::class);
            $existingFollow = $followRepository->findOneBy(['user' => $user, 'team' => $team]);
            
            if (!$existingFollow) {
                $follow = new Follow();
                $follow->setUser($user);
                $follow->setTeam($team);
                
                $entityManager = $doctrine->getManager();
                $entityManager->persist($follow);
                $entityManager->flush();
            }
        }
        
        // Carrega todos os times seguidos pelo usuário
        $followedTeams = $doctrine->getRepository(Follow::class)
            ->findBy(['user' => $user]);
        
        $teams = [];
        foreach ($followedTeams as $follow) {
            $teams[] = $follow->getTeam();
        }
        
        return $this->render('follow/follow.html.twig', [
            'id' => $team->getId(),
            'followed_teams' => $teams,
        ]);
    }
    
}
