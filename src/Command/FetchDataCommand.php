<?php

namespace App\Command;

use App\Entity\Coach;
use App\Entity\Player;
use App\Entity\RunningCompetition;
use App\Entity\Staff;
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
    protected static $defaultName = 'app:fetch-data';
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
        
        $this->fetchAndSaveTeams($io);
        
        return Command::SUCCESS;
    }
    
    private function fetchAndSaveTeams(SymfonyStyle $io): void
    {
        $teams = $this->apiService->getTeams();
        
        foreach ($teams as $teamData) {
            $team = new Team();
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
            
            // Map RunningCompetitions
            foreach ($teamData['runningCompetitions'] as $competitionData) {
                $competition = new RunningCompetition();
                $competition->setName($competitionData['name']);
                $competition->setCode($competitionData['code']);
                $competition->setType($competitionData['type']);
                $competition->setEmblem($competitionData['emblem']);
                $competition->setTeam($team);
                $this->entityManager->persist($competition);
            }
            
            // Map Coach
            $coachData = $teamData['coach'];
            $coach = new Coach();
            $coach->setFirstNameCoach($coachData['firstName']);
            $coach->setLastNameCoach($coachData['lastName']);
            $coach->setName($coachData['name']);
            $coach->setDate(new DateTime($coachData['dateOfBirth']));
            $coach->setNationality($coachData['nationality']);
            $coach->setContractStart($coachData['contract']['start']);
            $coach->setContractUntil($coachData['contract']['until']);
            $coach->setTeam($team);
            $this->entityManager->persist($coach);
            
            // Map Squad
            foreach ($teamData['squad'] as $playerData) {
                $player = new Player();
                /*$player->setFirstNamePlayer($playerData['firstName']);*/
                /*$player->setLastNamePlayer($playerData['lastName']);*/
                $player->setName($playerData['name']);
                $player->setPosition($playerData['position']);
                $player->setDate(new DateTime($playerData['dateOfBirth']));
                $player->setNationality($playerData['nationality']);
                /*$player->setShirtNumber($playerData['shirtNumber']);*/
                /*$player->setMarketValue($playerData['marketValue']);*/
                /*$player->setContractStart($playerData['contract']['start']);*/
                /*$player->setContractUntil($playerData['contract']['until']);*/
                $player->setTeam($team);
                $this->entityManager->persist($player);
            }
            
            // Map Staff
            foreach ($teamData['staff'] as $staffData) {
                $staff = new Staff();
                $staff->setFirstNameStaff($staffData['firstName']);
                $staff->setLastNameStaff($staffData['lastName']);
                $staff->setName($staffData['name']);
                $staff->setDate(new DateTime($staffData['dateOfBirth']));
                $staff->setNationality($staffData['nationality']);
                $staff->setContractStart($staffData['contract']['start']);
                $staff->setContractUntil($staffData['contract']['until']);
                $staff->setTeam($team);
                $this->entityManager->persist($staff);
                
            }
            
            $this->entityManager->persist($team);
        }
        
        $this->entityManager->flush();
        $io->success('Fetched and saved '.count($teams).' teams.');
    }
}
