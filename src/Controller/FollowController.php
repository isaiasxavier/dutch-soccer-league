<?php

namespace App\Controller;

use App\Repository\FollowRepository;
use App\Repository\SeasonTeamStandingRepository;
use App\Repository\StandingRepository;
use App\Repository\TeamRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[\AllowDynamicProperties] class FollowController extends AbstractController
{
    public function __construct(
        TeamRepository $teamRepository,
        FollowRepository $followRepository,
        SeasonTeamStandingRepository $seasonTeamStandingRepository,
        StandingRepository $standingRepository,
        ValidatorInterface $validator,
    ) {
        $this->teamRepository = $teamRepository;
        $this->followRepository = $followRepository;
        $this->seasonTeamStandingRepository = $seasonTeamStandingRepository;
        $this->standingRepository = $standingRepository;
        $this->validator = $validator;
    }

    #[Route('/follow/{id}', name: 'app_follow_action')]
    public function followTeam($id): Response
    {
        $user = $this->getUser();
        $team = $this->teamRepository->findTeamById($id);

        if ($user && $team) {
            $result = $this->followRepository->followTeamAction($user, $team);

            if (isset($result['errors'])) {
                return $this->render('follow/follow.html.twig', [
                    'errors' => $result['errors'],
                ]);
            }
        }

        return $this->redirectToRoute('app_follow');
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
