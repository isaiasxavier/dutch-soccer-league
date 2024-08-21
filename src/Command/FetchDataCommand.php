<?php

namespace App\Command;

use AllowDynamicProperties;
use App\Entity\Coach;
use App\Entity\Competition;
use App\Entity\GameMatch;
use App\Entity\Player;
use App\Entity\Season;
use App\Entity\SeasonTeamStanding;
use App\Entity\Standing;
use App\Entity\Team;
use App\Service\ApiService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[AllowDynamicProperties] #[AsCommand(
    name: 'FetchDataCommand',
    description: 'Add a short description for your command',
)]
class FetchDataCommand extends Command
{
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
    
    /**
     * @throws ORMException
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->fetchAndSaveTeam($io);
        $this->fetchAndSaveCompetition($io);
        $this->fetchAndSaveSeason($io);
        $this->fetchAndSaveCoach($io);
        $this->fetchAndSavePlayers($io);
        $this->fetchAndSaveStandings($io);
        $this->fetchAndSaveSeasonTeamStandings($io);
        $this->fetchAndSaveGameMatches($io);

        return Command::SUCCESS;
    }
    
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws ORMException
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    private function fetchAndSaveTeam(SymfonyStyle $io): void
    {
        $teams = $this->apiService->getTeams();
        
        foreach ($teams as $teamData) {
            try {
                $this->saveTeam($teamData);
            } catch (Exception $error) {
                $io->error('Error saving team: '.$error->getMessage());
            }
        }
        
        $this->entityManager->flush();
        $io->success('Fetched and saved team table');
    }
    
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws Exception
     */
    private function saveTeam(array $teamData): void
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
        $team->setLastUpdated(new DateTime($teamData['lastUpdated']));

        $this->entityManager->persist($team);
    }
    
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws ORMException
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    private function fetchAndSaveCompetition(SymfonyStyle $io): void
    {
        $teams = $this->apiService->getTeams();
        
        foreach ($teams as $teamData) {
            try {
                foreach ($teamData['runningCompetitions'] as $competitionData) {
                    $this->saveCompetition($competitionData);
                }
            } catch (Exception $error) {
                $io->error('Error saving competition: '.$error->getMessage());
            }
        }
        
        $this->entityManager->flush();
        $io->success('Fetched and saved competition table');
    }
    
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    private function saveCompetition(array $competitionData): void
    {
        $competition = $this->entityManager->find(Competition::class, $competitionData['id']) ?? new Competition();
        $competition->setId($competitionData['id']);
        $competition->setName($competitionData['name']);
        $competition->setCode($competitionData['code']);
        $competition->setType($competitionData['type']);
        $competition->setEmblem($competitionData['emblem']);
        
        $this->entityManager->persist($competition);
    }
    
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws ORMException
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    private function fetchAndSaveSeason(SymfonyStyle $io): void
    {
        $standings = $this->apiService->getStanding();
        
            try {
                $competitionData = $standings['competition'];
                $competition = $this->entityManager->getRepository(Competition::class)->find($competitionData['id']);
                
                if ($competition) {
                    $seasonData = $standings['season'];
                    $this->saveSeason($seasonData, $competition);
                } else {
                    $io->error('Competition not found for the season.');
                }
            } catch (Exception $error) {
                $io->error('Error saving season: '.$error->getMessage());
            }
            
        $this->entityManager->flush();
        $io->success('Fetched and saved season table.');
    }
    
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws Exception
     */
    private function saveSeason(array $seasonData, Competition $competition): void
    {
        $season = $this->entityManager->find(Season::class, $seasonData['id']) ?? new Season();
        $season->setId($seasonData['id']);
        $season->setStartDate(new DateTime($seasonData['startDate']));
        $season->setEndDate(new DateTime($seasonData['endDate']));
        $season->setCurrentMatchday($seasonData['currentMatchday']);
        $season->setWinner($seasonData['winner']);
        $season->setCompetition($competition);
        
        $this->entityManager->persist($season);
    }
    
    /**
     * @throws TransportExceptionInterface
     * @throws ORMException
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    private function fetchAndSavePlayers(SymfonyStyle $io): void
    {
        $teams = $this->apiService->getTeams();
        
        foreach ($teams as $teamData) {
            try {
                $team = $this->entityManager->find(Team::class, $teamData['id']);
                foreach ($teamData['squad'] as $playerData) {
                    $this->savePlayer($playerData, $team);
                }
            } catch (Exception $error) {
                $io->error('Error saving players: '.$error->getMessage());
            }
        }
        
        $this->entityManager->flush();
        $io->success('Fetched and saved player table');
    }
    
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws Exception
     */
    private function savePlayer(array $playerData, Team $team): void
    {
        $player = $this->entityManager->find(Player::class, $playerData['id']) ?? new Player();
        $player->setId($playerData['id']);
        $player->setName($playerData['name']);
        $player->setPosition($playerData['position']);
        $player->setDate(new DateTime($playerData['dateOfBirth']));
        $player->setNationality($playerData['nationality']);
        $player->setTeam($team);
        
        $this->entityManager->persist($player);
    }
    
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws ORMException
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    private function fetchAndSaveStandings(SymfonyStyle $io): void
    {
        $standings = $this->apiService->getStanding();
        
        foreach ($standings['standings'] as $standingData) {
            try {
                $competitionData = $standings['competition'];
                $competition = $this->entityManager->getRepository(Competition::class)->find($competitionData['id']);
                
                if ($competition) {
                    $seasonData = $standings['season'];
                    $season = $this->entityManager->getRepository(Season::class)->find($seasonData['id']);
                    
                    if ($season) {
                        $this->saveStanding($standingData, $season);
                    } else {
                        $io->error('Season not found for the standings.');
                    }
                } else {
                    $io->error('Competition not found for the standings.');
                }
            } catch (Exception $error) {
                $io->error('Error saving standing: ' . $error->getMessage());
            }
        }
        
        $this->entityManager->flush();
        $io->success('Fetched and saved standing table.');
    }
    
    private function saveStanding(array $standingData, Season $season): void
    {
        $standing = $this->entityManager->getRepository(Standing::class)->findOneBy([
            'season' => $season
        ]) ?? new Standing();
        $standing->setStage($standingData['stage']);
        $standing->setType($standingData['type']);
        $standing->setGroupName($standingData['group']);
        $standing->setSeason($season);
        
        $this->entityManager->persist($standing);
    }
    
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws ORMException
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    private function fetchAndSaveSeasonTeamStandings(SymfonyStyle $io): void
    {
        $standings = $this->apiService->getStanding();
        
        $seasonData = $standings['season'];
        $season = $this->entityManager->getRepository(Season::class)->find($seasonData['id']);
        
        foreach ($standings['standings'] as $standingData) {
            try {
                $standing = $this->entityManager->getRepository(Standing::class)->findOneBy([
                    'season' => $season,
                ]);
                
                if ($standing) {
                    foreach ($standingData['table'] as $teamStandingData) {
                        $this->saveSeasonTeamStanding($teamStandingData, $standing, $season);
                    }
                }
            } catch (Exception $error) {
                $io->error('Error saving season team standings: '.$error->getMessage());
            }
        }
        
        $this->entityManager->flush();
        $io->success('Fetched and saved season_team_standing table.');
    }
    
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    private function saveSeasonTeamStanding($teamStandingData, Standing $standing, Season $season): void
    {
        $teamData = $teamStandingData['team'];
        
        $team = $this->entityManager->find(Team::class, $teamData['id']);
        
        $seasonTeamStanding = $this->entityManager->getRepository(SeasonTeamStanding::class)->findOneBy([
            'standing' => $standing,
            'team' => $team,
        ]) ?? new SeasonTeamStanding();
        
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
    
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws ORMException
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    private function fetchAndSaveCoach(SymfonyStyle $io): void
    {
        $teams = $this->apiService->getTeams();
        
        foreach ($teams as $teamData) {
            try {
                $team = $this->entityManager->find(Team::class, $teamData['id']);
                $this->saveCoach($teamData['coach'], $team);
            } catch (Exception $error) {
                $io->error('Error saving coach: '.$error->getMessage());
            }
        }
        
        $this->entityManager->flush();
        $io->success('Fetched and saved coach table');
    }
    
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws Exception
     */
    private function saveCoach(array $coachData, Team $team): void
    {
        $coach = $this->entityManager->find(Coach::class, $coachData['id']) ?? new Coach();
        $coach->setId($coachData['id']);
        $coach->setFirstName($coachData['firstName']);
        $coach->setLastName($coachData['lastName']);
        $coach->setDate(new DateTime($coachData['dateOfBirth']));
        $coach->setNationality($coachData['nationality']);
        $coach->setContractStart($coachData['contract']['start']);
        $coach->setContractUntil($coachData['contract']['until']);
        $coach->setTeam($team);

        $this->entityManager->persist($coach);
    }
    
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws ORMException
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    private function fetchAndSaveGameMatches(SymfonyStyle $io): void
    {
        $matches = $this->apiService->getMatchesDed();
        
        foreach ($matches['matches'] as $matchData) {
            try {
                $this->saveGameMatch($matchData);
            } catch (Exception $error) {
                $io->error('Error saving game match: '.$error->getMessage());
            }
        }
        
        $this->entityManager->flush();
        $io->success('Fetched and saved game_match table.');
    }
    
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws Exception
     */
    private function saveGameMatch(array $matchData): void
    {
        $gameMatch = $this->entityManager->find(GameMatch::class, $matchData['id']) ?? new GameMatch();
        $gameMatch->setStatus($matchData['status']);
        $gameMatch->setMatchday($matchData['matchday']);
        $gameMatch->setStage($matchData['stage']);
        $gameMatch->setLastUpdated(new DateTime($matchData['lastUpdated']));
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
        $gameMatch->setDateGame(new DateTime($matchData['utcDate']));

        $homeTeam = $this->entityManager->find(Team::class, $matchData['homeTeam']['id']);
        $awayTeam = $this->entityManager->find(Team::class, $matchData['awayTeam']['id']);
        $gameMatch->setHomeTeam($homeTeam);
        $gameMatch->setAwayTeam($awayTeam);

        $this->entityManager->persist($gameMatch);
    }
    
}
