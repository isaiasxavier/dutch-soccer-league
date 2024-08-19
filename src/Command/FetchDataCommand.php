<?php

namespace App\Command;

use App\Entity\Coach;
use App\Entity\Competition;
use App\Entity\GameMatch;
use App\Entity\Player;
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
    private ApiService $apiService;
    private EntityManagerInterface $entityManager;

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

        $this->fetchAndSaveTeamsCompetitionPlayers($io);
        $this->fetchAndSaveSeasons($io);
        $this->fetchAndSaveStandings($io);
        $this->fetchAndSaveSeasonTeamStandings($io);
        $this->fetchAndSaveGameMatches($io);

        return Command::SUCCESS;
    }

    private function saveTeam(array $teamData): Team
    {
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

        $this->entityManager->persist($team);

        return $team;
    }

    private function saveCompetition(array $competitionData, ?Team $team = null): Competition
    {
        $competition = $this->entityManager->find(Competition::class, $competitionData['id']) ?? new Competition();
        $competition->setId($competitionData['id']);
        $competition->setName($competitionData['name']);
        $competition->setCode($competitionData['code']);
        $competition->setType($competitionData['type']);
        $competition->setEmblem($competitionData['emblem']);

        if ($team) {
            $competition->setTeam($team);
        }

        $this->entityManager->persist($competition);

        return $competition;
    }

    private function saveCoach(array $coachData, Team $team): void
    {
        $coach = $this->entityManager->find(Coach::class, $coachData['id']) ?? new Coach();
        $coach->setId($coachData['id']);
        $coach->setFirstName($coachData['firstName']);
        $coach->setLastName($coachData['lastName']);
        $coach->setDate(new \DateTime($coachData['dateOfBirth']));
        $coach->setNationality($coachData['nationality']);
        $coach->setContractStart($coachData['contract']['start']);
        $coach->setContractUntil($coachData['contract']['until']);
        $coach->setTeam($team);

        $this->entityManager->persist($coach);
    }

    private function savePlayer(array $playerData, Team $team): void
    {
        $player = $this->entityManager->find(Player::class, $playerData['id']) ?? new Player();
        $player->setId($playerData['id']);
        $player->setName($playerData['name']);
        $player->setPosition($playerData['position']);
        $player->setDate(new \DateTime($playerData['dateOfBirth']));
        $player->setNationality($playerData['nationality']);
        $player->setTeam($team);

        $this->entityManager->persist($player);
    }

    private function saveSeason(array $seasonData, Competition $competition): Season
    {
        $season = $this->entityManager->find(Season::class, $seasonData['id']) ?? new Season();
        $season->setId($seasonData['id']);
        $season->setStartDate(new \DateTime($seasonData['startDate']));
        $season->setEndDate(new \DateTime($seasonData['endDate']));
        $season->setCurrentMatchday($seasonData['currentMatchday']);
        $season->setWinner($seasonData['winner']);
        $season->setCompetition($competition);

        $this->entityManager->persist($season);

        return $season;
    }

    private function saveStanding(array $standingData, Season $season): Standing
    {
        $standing = $this->entityManager->getRepository(Standing::class)->findOneBy([
            'stage' => $standingData['stage'],
            'type' => $standingData['type'],
        ]) ?? new Standing();
        $standing->setStage($standingData['stage']);
        $standing->setType($standingData['type']);
        $standing->setGroupName($standingData['group']);
        $standing->setSeason($season);

        $this->entityManager->persist($standing);

        return $standing;
    }

    private function saveSeasonTeamStanding(array $teamStandingData, Standing $standing): void
    {
        if (isset($teamStandingData['team'])) {
            $teamData = $teamStandingData['team'];

            $team = $this->entityManager->find(Team::class, $teamData['id']);
            if (!$team) {
                $team->setAddress($teamData['address'] ?? null);
                $this->entityManager->persist($team);
            }
        }

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

    private function saveGameMatch(array $matchData): void
    {
        $gameMatch = $this->entityManager->find(GameMatch::class, $matchData['id']) ?? new GameMatch();
        $gameMatch->setStatus($matchData['status']);
        $gameMatch->setMatchday($matchData['matchday']);
        $gameMatch->setStage($matchData['stage']);
        $gameMatch->setLastUpdated(new \DateTime($matchData['lastUpdated']));
        $gameMatch->setHomeTeamId($matchData['homeTeam']['id']);
        $gameMatch->setAwayTeamId($matchData['awayTeam']['id']);
        $gameMatch->setHomeTeamScoreFullTime($matchData['score']['fullTime']['home']);
        $gameMatch->setAwayTeamScoreFullTime($matchData['score']['fullTime']['away']);
        $gameMatch->setHomeTeamScoreHalfTime($matchData['score']['halfTime']['home']);
        $gameMatch->setAwayTeamScoreHalfTime($matchData['score']['halfTime']['away']);
        $gameMatch->setScoreWinner($matchData['score']['winner']);
        $gameMatch->setScoreDuration($matchData['score']['duration']);
        $gameMatch->setRefereeId($matchData['referees'][0]['id'] ?? null);
        $gameMatch->setRefereeName($matchData['referees'][0]['name'] ?? null);
        $gameMatch->setDateGame(new \DateTime($matchData['utcDate']));

        $homeTeam = $this->entityManager->find(Team::class, $matchData['homeTeam']['id']);
        $awayTeam = $this->entityManager->find(Team::class, $matchData['awayTeam']['id']);
        $gameMatch->setHomeTeam($homeTeam);
        $gameMatch->setAwayTeam($awayTeam);

        $this->entityManager->persist($gameMatch);
    }

    private function fetchAndSaveTeamsCompetitionPlayers(SymfonyStyle $io): void
    {
        $teams = $this->apiService->getTeams();

        foreach ($teams as $teamData) {
            try {
                $team = $this->saveTeam($teamData);

                foreach ($teamData['runningCompetitions'] as $competitionData) {
                    $this->saveCompetition($competitionData, $team);
                }

                $this->saveCoach($teamData['coach'], $team);

                foreach ($teamData['squad'] as $playerData) {
                    $this->savePlayer($playerData, $team);
                }
            } catch (\Exception $e) {
                $io->error('Error saving team: '.$e->getMessage());
            }
        }

        $this->entityManager->flush();
        $io->success('Fetched and saved team, competition and player tables');
    }

    private function fetchAndSaveSeasons(SymfonyStyle $io): void
    {
        $standings = $this->apiService->getStanding();

        foreach ($standings['standings'] as $standingData) {
            try {
                $competitionData = $standings['competition'];
                $competition = $this->saveCompetition($competitionData);

                $seasonData = $standings['season'];
                $this->saveSeason($seasonData, $competition);
            } catch (\Exception $e) {
                $io->error('Error saving season: '.$e->getMessage());
            }
        }

        $this->entityManager->flush();
        $io->success('Fetched and saved season table.');
    }

    private function fetchAndSaveStandings(SymfonyStyle $io): void
    {
        $standings = $this->apiService->getStanding();

        foreach ($standings['standings'] as $standingData) {
            try {
                $seasonData = $standings['season'];
                $season = $this->saveSeason($seasonData, $this->saveCompetition($standings['competition']));

                $this->saveStanding($standingData, $season);
            } catch (\Exception $e) {
                $io->error('Error saving standing: '.$e->getMessage());
            }
        }

        $this->entityManager->flush();
        $io->success('Fetched and saved standing table.');
    }

    private function fetchAndSaveSeasonTeamStandings(SymfonyStyle $io): void
    {
        $standings = $this->apiService->getStanding();

        foreach ($standings['standings'] as $standingData) {
            try {
                $standing = $this->entityManager->getRepository(Standing::class)->findOneBy([
                    'stage' => $standingData['stage'],
                    'type' => $standingData['type'],
                ]);

                if ($standing) {
                    foreach ($standingData['table'] as $teamStandingData) {
                        $this->saveSeasonTeamStanding($teamStandingData, $standing);
                    }
                }
            } catch (\Exception $e) {
                $io->error('Error saving season team standings: '.$e->getMessage());
            }
        }

        $this->entityManager->flush();
        $io->success('Fetched and saved season_team_standing table.');
    }

    private function fetchAndSaveGameMatches(SymfonyStyle $io): void
    {
        $matches = $this->apiService->getMatchesDed();

        foreach ($matches['matches'] as $matchData) {
            try {
                $this->saveGameMatch($matchData);
            } catch (\Exception $e) {
                $io->error('Error saving game match: '.$e->getMessage());
            }
        }

        $this->entityManager->flush();
        $io->success('Fetched and saved game_match table.');
    }
}
