<?php

namespace App\Repository;

use App\Entity\Follow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @extends ServiceEntityRepository<Follow>
 *
 * @method Follow|null find($id, $lockMode = null, $lockVersion = null)
 * @method Follow|null findOneBy(array $criteria, array $orderBy = null)
 * @method Follow[]    findAll()
 * @method Follow[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
#[\AllowDynamicProperties] class FollowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, ValidatorInterface $validator)
    {
        parent::__construct($registry, Follow::class);
        $this->validator = $validator;
    }

    public function getFollowedTeamIds($user): array
    {
        $followedTeams = $this->findBy(['user' => $user]);

        $followedTeamIds = [];
        foreach ($followedTeams as $follow) {
            $followedTeamIds[] = $follow->getTeam()->getId();
        }

        return $followedTeamIds;
    }

    public function followTeamAction($user, $team): array
    {
        $existingFollow = $this->findOneBy(['user' => $user, 'team' => $team]);

        if (!$existingFollow) {
            $follow = new Follow();
            $follow->setUser($user);
            $follow->setTeam($team);

            $errors = $this->validator->validate($follow);
            if (count($errors) > 0) {
                return ['errors' => $errors];
            }

            $entityManager = $this->getEntityManager();
            $entityManager->persist($follow);
            $entityManager->flush();
        }

        return ['success' => true];
    }

    public function unfollowTeam($user, $teamId): void
    {
        $follow = $this->findOneBy(['user' => $user, 'team' => $teamId]);

        if ($follow) {
            $entityManager = $this->getEntityManager();
            $entityManager->remove($follow);
            $entityManager->flush();
        }
    }
}
