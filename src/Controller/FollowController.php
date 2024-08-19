<?php

namespace App\Controller;

use App\Repository\FollowRepository;
use App\Repository\SeasonTeamStandingRepository;
use App\Repository\StandingRepository;
use App\Repository\TeamRepository;
use App\Validator\FollowValidator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[\AllowDynamicProperties] class FollowController extends AbstractController
{
    public function __construct(
        TeamRepository $teamRepository,
        FollowRepository $followRepository,
        SeasonTeamStandingRepository $seasonTeamStandingRepository,
        StandingRepository $standingRepository,
        FollowValidator $followValidator,
    ) {
        $this->teamRepository = $teamRepository;
        $this->followRepository = $followRepository;
        $this->seasonTeamStandingRepository = $seasonTeamStandingRepository;
        $this->standingRepository = $standingRepository;
        $this->followValidator = $followValidator;
    }

    #[Route('/follow/{id}', name: 'app_follow_action')]
    public function followTeam($id): Response
    {
        $user = $this->getUser();
        
        $team = $this->teamRepository->findTeamById($id);
        
        $result = $this->followRepository->followTeamAction($user, $team);

        $followedTeams = $this->followRepository->getFollowedTeamsByUser($user);
        
        return $this->render('follow/follow.html.twig', [
            'errors' => $result['errors'] ?? [],
            'followed_teams' => $followedTeams,
        ]);
    }

    #[Route('/followed-teams', name: 'app_follow')]
    public function index(): Response
    {
        $user = $this->getUser();

        $followedTeams = $this->followRepository->getFollowedTeamIds($user);

        $teams = $this->teamRepository->findTeamsByIds($followedTeams);

        return $this->render('follow/follow.html.twig', [
            'followed_teams' => $teams,
        ]);
    }

    #[Route('/unfollow/{id}', name: 'app_unfollow')]
    public function unfollow(int $id, ManagerRegistry $doctrine): RedirectResponse
    {
        $user = $this->getUser();

        $this->followRepository->unfollowTeam($user, $id);

        return $this->redirectToRoute('app_follow');
    }
}
