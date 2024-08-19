<?php

namespace App\Controller;

use App\Entity\Follow;
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

                $errors = $this->validator->validate($follow);
                if (count($errors) > 0) {
                    $followedTeams = $doctrine->getRepository(Follow::class)
                        ->findBy(['user' => $user]);

                    $teams = [];
                    foreach ($followedTeams as $followed) {
                        $teams[] = $followed->getTeam();
                    }

                    return $this->render('follow/follow.html.twig', [
                        'errors' => $errors,
                    ]);
                }

                $entityManager = $doctrine->getManager();
                $entityManager->persist($follow);
                $entityManager->flush();
            }
        }

        return $this->redirectToRoute('app_follow');
    }

    #[Route('/followed-teams', name: 'app_follow')]
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

    #[Route('/unfollow/{id}', name: 'app_unfollow')]
    public function unfollow(int $id, ManagerRegistry $doctrine): RedirectResponse
    {
        $user = $this->getUser();
        $follow = $doctrine->getRepository(Follow::class)
            ->findOneBy(['user' => $user, 'team' => $id]);

        if ($follow) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($follow);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_follow');
    }
}
