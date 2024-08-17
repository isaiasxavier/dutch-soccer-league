<?php

namespace App\Command;

use App\Entity\Coach;
use App\Entity\Player;
use App\Entity\RunningCompetition;
use App\Entity\Team;
use App\Service\ApiService;
use DateTime;
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

        $this->fetchAndSaveData($io);

        return Command::SUCCESS;
    }
    
    private function fetchAndSaveData(SymfonyStyle $io): void
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
            $team->setLastUpdated(new DateTime($teamData['lastUpdated']));
            
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
            $coach->setDate(new DateTime($coachData['dateOfBirth']));
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
                $player->setDate(new DateTime($playerData['dateOfBirth']));
                $player->setNationality($playerData['nationality']);
                $player->setTeam($team);
                $this->entityManager->persist($player);
            }
        }
        
        $this->entityManager->flush();
        $io->success('Fetched and saved '.count($teams).' teams.');
    }
}
