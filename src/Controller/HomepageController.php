<?php

namespace App\Controller;

use App\Repository\CompetitionRepository;
use App\Repository\SeasonRepository;
use App\Repository\SeasonTeamStandingRepository;
use App\Repository\StandingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    ): Response {
        
        $competition = $competitionRepository->findByName('Eredivisie');
        
        $season = $seasonRepository->findCurrentSeasonByCompetition($competition);
        
        $standings = $standingRepository->findBySeason($season);

        $seasonTeamStandings = $seasonTeamStandingRepository->findByStandings($standings);

        return $this->render('homepage/homepage.html.twig', [
            'competition' => $competition,
            'season' => $season,
            'standings' => $standings,
            'seasonTeamStandings' => $seasonTeamStandings,
        ]);
    }
}
