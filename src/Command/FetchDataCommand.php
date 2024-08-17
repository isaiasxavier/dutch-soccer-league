<?php

namespace App\Command;

use App\Entity\Coach;
use App\Entity\Competition;
use App\Entity\GameMatch;
use App\Entity\Player;
use App\Entity\RunningCompetition;
use App\Entity\Season;
use App\Entity\SeasonTeamStanding;
use App\Entity\Standing;
use App\Entity\Team;
use App\Service\ApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'FetchDataCommand',
    description: 'Add a short description for your command',
)]
class FetchDataCommand extends Command
{
    private $apiService;
    private $entityManager;

    public function __construct(ApiService $apiService, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->apiService = $apiService;
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this->setDescription('Fetch data from external API');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->fetchAndSaveDataTeam($io);
        $this->fetchAndSaveCompetitionAndSeason($io);
        $this->fetchAndSaveDataStanding($io);
        $this->fetchAndSaveSeasonTeamStanding($io);
        $this->fetchAndSaveGameMatch($io);

        return Command::SUCCESS;
    }

    private function fetchAndSaveDataTeam(SymfonyStyle $io): void
    {
        $teams = $this->apiService->getTeams();

        foreach ($teams as $teamData) {
            $team = $this->entityManager->find(Team::class, $teamData['id']) ?? new Team();
            $team->setId($teamData['id']);
            $team->setName($teamData['name']);
            $team->setShortName($teamData['shortName']);
            $team->setTla($teamData['tla']);
            $team->setCrest($teamData['crest']);
            $team->setAddress($teamData['address']);
            $team->setWebsite($teamData['website']);
            $team->setFounded($teamData['founded']);
            $team->setClubColors($teamData['clubColors']);
            $team->setVenue($teamData['venue']);
            $team->setLastUpdated(new \DateTime($teamData['lastUpdated']));

            foreach ($teamData['runningCompetitions'] as $competitionData) {
                $competition = $this->entityManager->find(RunningCompetition::class, $competitionData['id']) ?? new RunningCompetition();
                $competition->setId($competitionData['id']);
                $competition->setName($competitionData['name']);
                $competition->setCode($competitionData['code']);
                $competition->setType($competitionData['type']);
                $competition->setEmblem($competitionData['emblem']);
                $competition->setTeam($team);
                $this->entityManager->persist($competition);
            }

            $coachData = $teamData['coach'];
            $coach = $this->entityManager->find(Coach::class, $coachData['id']) ?? new Coach();
            $coach->setId($coachData['id']);
            $coach->setFirstName($coachData['firstName']);
            $coach->setLastName($coachData['lastName']);
            $coach->setName($coachData['name']);
            $coach->setDate(new \DateTime($coachData['dateOfBirth']));
            $coach->setNationality($coachData['nationality']);
            $coach->setContractStart($coachData['contract']['start']);
            $coach->setContractUntil($coachData['contract']['until']);
            $coach->setTeam($team);
            $this->entityManager->persist($coach);

            foreach ($teamData['squad'] as $playerData) {
                $player = $this->entityManager->find(Player::class, $playerData['id']) ?? new Player();
                $player->setId($playerData['id']);
                $player->setName($playerData['name']);
                $player->setPosition($playerData['position']);
                $player->setDate(new \DateTime($playerData['dateOfBirth']));
                $player->setNationality($playerData['nationality']);
                $player->setTeam($team);
                $this->entityManager->persist($player);
            }
        }

        $this->entityManager->flush();
        $io->success('Fetched and saved '.count($teams).' teams.');
    }

    private function fetchAndSaveCompetitionAndSeason(SymfonyStyle $io): void
    {
        $standings = $this->apiService->getStanding();

        foreach ($standings['standings'] as $standingData) {
            $competitionData = $standings['competition'];
            $competition = $this->entityManager->find(Competition::class, $competitionData['id']) ?? new Competition();
            $competition->setId($competitionData['id']);
            $competition->setName($competitionData['name']);
            $competition->setCode($competitionData['code']);
            $competition->setType($competitionData['type']);
            $competition->setEmblem($competitionData['emblem']);
            $this->entityManager->persist($competition);

            $seasonData = $standings['season'];
            $season = $this->entityManager->find(Season::class, $seasonData['id']) ?? new Season();
            $season->setId($seasonData['id']);
            $season->setStartDate(new \DateTime($seasonData['startDate']));
            $season->setEndDate(new \DateTime($seasonData['endDate']));
            $season->setCurrentMatchday($seasonData['currentMatchday']);
            $season->setWinner($seasonData['winner']);
            $season->setCompetition($competition);
            $this->entityManager->persist($season);
        }

        $this->entityManager->flush();
        $io->success('Fetched and saved competition and season.');
    }

    private function fetchAndSaveDataStanding(SymfonyStyle $io): void
    {
        $standings = $this->apiService->getStanding();

        foreach ($standings['standings'] as $standingData) {
            $seasonData = $standings['season'];
            $season = $this->entityManager->find(Season::class, $seasonData['id']) ?? new Season();
            $season->setId($seasonData['id']);
            $season->setStartDate(new \DateTime($seasonData['startDate']));
            $season->setEndDate(new \DateTime($seasonData['endDate']));
            $season->setCurrentMatchday($seasonData['currentMatchday']);
            $season->setWinner($seasonData['winner']);
            $this->entityManager->persist($season);

            $standing = $this->entityManager->getRepository(Standing::class)->findOneBy([
                'stage' => $standingData['stage'],
                'type' => $standingData['type'],
            ]) ?? new Standing();
            $standing->setStage($standingData['stage']);
            $standing->setType($standingData['type']);
            $standing->setGroupName($standingData['group']);
            $standing->setSeason($season);
            $this->entityManager->persist($standing);
        }

        $this->entityManager->flush();
        $io->success('Fetched and saved standings.');
    }

    private function fetchAndSaveSeasonTeamStanding(SymfonyStyle $io): void
    {
        $standings = $this->apiService->getStanding();

        foreach ($standings['standings'] as $standingData) {
            $standing = $this->entityManager->getRepository(Standing::class)->findOneBy([
                'stage' => $standingData['stage'],
                'type' => $standingData['type'],
            ]);

            if ($standing) {
                foreach ($standingData['table'] as $teamStandingData) {
                    $team = $this->entityManager->find(Team::class, $teamStandingData['team']['id']) ?? new Team();
                    $team->setId($teamStandingData['team']['id']);
                    $team->setName($teamStandingData['team']['name']);
                    $team->setShortName($teamStandingData['team']['shortName']);
                    $team->setTla($teamStandingData['team']['tla']);
                    $team->setCrest($teamStandingData['team']['crest']);
                    $this->entityManager->persist($team);

                    $seasonTeamStanding = new SeasonTeamStanding();
                    $seasonTeamStanding->setPosition($teamStandingData['position']);
                    $seasonTeamStanding->setPlayedGames($teamStandingData['playedGames']);
                    $seasonTeamStanding->setForm($teamStandingData['form']);
                    $seasonTeamStanding->setWon($teamStandingData['won']);
                    $seasonTeamStanding->setDraw($teamStandingData['draw']);
                    $seasonTeamStanding->setLost($teamStandingData['lost']);
                    $seasonTeamStanding->setPoints($teamStandingData['points']);
                    $seasonTeamStanding->setGoalsFor($teamStandingData['goalsFor']);
                    $seasonTeamStanding->setGoalsAgainst($teamStandingData['goalsAgainst']);
                    $seasonTeamStanding->setGoalDifference($teamStandingData['goalDifference']);
                    $seasonTeamStanding->setStanding($standing);
                    $seasonTeamStanding->setTeam($team);
                    $this->entityManager->persist($seasonTeamStanding);
                }
            }
        }

        $this->entityManager->flush();
        $io->success('Fetched and saved season team standings.');
    }

    private function fetchAndSaveGameMatch(SymfonyStyle $io): void
    {
        $matches = $this->apiService->getMatchesDed();

        foreach ($matches['matches'] as $matchData) {
            $gameMatch = $this->entityManager->find(GameMatch::class, $matchData['id']) ?? new GameMatch();
            $gameMatch->setStatus($matchData['status']);
            $gameMatch->setMatchday($matchData['matchday']);
            $gameMatch->setStage($matchData['stage']);
            $gameMatch->setLastUpdated(new \DateTime($matchData['lastUpdated']));
            $gameMatch->setHomeTeamId($matchData['homeTeam']['id']);
            $gameMatch->setHomeTeamName($matchData['homeTeam']['name']);
            $gameMatch->setAwayTeamId($matchData['awayTeam']['id']);
            $gameMatch->setAwayTeamName($matchData['awayTeam']['name']);
            $gameMatch->setHomeTeamScoreFullTime($matchData['score']['fullTime']['home']);
            $gameMatch->setAwayTeamScoreFullTime($matchData['score']['fullTime']['away']);
            $gameMatch->setHomeTeamScoreHalfTime($matchData['score']['halfTime']['home']);
            $gameMatch->setAwayTeamScoreHalfTime($matchData['score']['halfTime']['away']);
            $gameMatch->setScoreWinner($matchData['score']['winner']);
            $gameMatch->setScoreDuration($matchData['score']['duration']);
            $gameMatch->setRefereeId($matchData['referees'][0]['id'] ?? null);
            $gameMatch->setRefereeName($matchData['referees'][0]['name'] ?? null);

            $homeTeam = $this->entityManager->find(Team::class, $matchData['homeTeam']['id']);
            $awayTeam = $this->entityManager->find(Team::class, $matchData['awayTeam']['id']);
            $gameMatch->setHomeTeam($homeTeam);
            $gameMatch->setAwayTeam($awayTeam);

            $this->entityManager->persist($gameMatch);
        }

        $this->entityManager->flush();
        $io->success('Fetched and saved game matches.');
    }
}
