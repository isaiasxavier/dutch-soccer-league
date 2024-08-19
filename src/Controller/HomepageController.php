<?php

namespace App\Controller;

use App\Repository\CompetitionRepository;
use App\Repository\SeasonRepository;
use App\Repository\SeasonTeamStandingRepository;
use App\Repository\StandingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(
        SeasonRepository $seasonRepository,
        CompetitionRepository $competitionRepository,
        StandingRepository $standingRepository,
        SeasonTeamStandingRepository $seasonTeamStandingRepository,
        Request $request,
    ): Response {
        // Buscar a competição Eredivisie
        $competition = $competitionRepository->findOneBy(['name' => 'Eredivisie']);

        // Buscar a temporada atual da Eredivisie
        $season = $seasonRepository->findOneBy(['competition' => $competition]);

        // Buscar as standings da temporada atual
        $standings = $standingRepository->findBy(['season' => $season]);

        // Buscar os dados de SeasonTeamStanding relacionados às standings com paginação
        $seasonTeamStandings = $seasonTeamStandingRepository->createQueryBuilder('sts')
            ->where('sts.standing IN (:standings)')
            ->setParameter('standings', $standings)
            ->getQuery()
            ->getResult();

        return $this->render('homepage/homepage.html.twig', [
            'competition' => $competition,
            'season' => $season,
            'standings' => $standings,
            'seasonTeamStandings' => $seasonTeamStandings,
        ]);
    }
}
