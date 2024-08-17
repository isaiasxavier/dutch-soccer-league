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
    name: 'UpdateDataCommand',
    description: 'Add a short description for your command',
)]
class UpdateDataCommand extends Command
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
        $this->setDescription('Update data from external API');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->updateData($io);

        return Command::SUCCESS;
    }
    
    private function updateData(SymfonyStyle $io): void
    {
        $teams = $this->apiService->getTeams();
        
        foreach ($teams as $teamData) {
            $team = $this->entityManager->find(Team::class, $teamData['id']);
            
            if ($team) {
                $updated = false;
                
                if ($team->getName() !== $teamData['shortName']) {
                    $team->setName($teamData['shortName']);
                    $updated = true;
                }
                if ($team->getShortName() !== $teamData['shortName']) {
                    $team->setShortName($teamData['shortName']);
                    $updated = true;
                }
                if ($team->getTla() !== $teamData['tla']) {
                    $team->setTla($teamData['tla']);
                    $updated = true;
                }
                if ($team->getCrest() !== $teamData['crest']) {
                    $team->setCrest($teamData['crest']);
                    $updated = true;
                }
                if ($team->getAddress() !== $teamData['address']) {
                    $team->setAddress($teamData['address']);
                    $updated = true;
                }
                if ($team->getWebsite() !== $teamData['website']) {
                    $team->setWebsite($teamData['website']);
                    $updated = true;
                }
                if ($team->getFounded() !== $teamData['founded']) {
                    $team->setFounded($teamData['founded']);
                    $updated = true;
                }
                if ($team->getClubColors() !== $teamData['clubColors']) {
                    $team->setClubColors($teamData['clubColors']);
                    $updated = true;
                }
                if ($team->getVenue() !== $teamData['venue']) {
                    $team->setVenue($teamData['venue']);
                    $updated = true;
                }
                if ($team->getLastUpdated() != new DateTime($teamData['lastUpdated'])) {
                    $team->setLastUpdated(new DateTime($teamData['lastUpdated']));
                    $updated = true;
                }
                
                foreach ($teamData['runningCompetitions'] as $competitionData) {
                    $competition = $this->entityManager->find(RunningCompetition::class, $competitionData['id']);
                    
                    if ($competition) {
                        if ($competition->getName() !== $competitionData['name']) {
                            $competition->setName($competitionData['name']);
                            $updated = true;
                        }
                        if ($competition->getCode() !== $competitionData['code']) {
                            $competition->setCode($competitionData['code']);
                            $updated = true;
                        }
                        if ($competition->getType() !== $competitionData['type']) {
                            $competition->setType($competitionData['type']);
                            $updated = true;
                        }
                        if ($competition->getEmblem() !== $competitionData['emblem']) {
                            $competition->setEmblem($competitionData['emblem']);
                            $updated = true;
                        }
                    }
                }
                
                $coachData = $teamData['coach'];
                $coach = $this->entityManager->find(Coach::class, $coachData['id']);
                if ($coach) {
                    if ($coach->getName() !== $coachData['name']) {
                        $coach->setName($coachData['name']);
                        $updated = true;
                    }
                    if ($coach->getFirstName() !== $coachData['firstName']) {
                        $coach->setFirstName($coachData['firstName']);
                        $updated = true;
                    }
                    if ($coach->getLastName() !== $coachData['lastName']) {
                        $coach->setLastName($coachData['lastName']);
                        $updated = true;
                    }
                    if ($coach->getDate() != new DateTime($coachData['dateOfBirth'])) {
                        $coach->setDate(new DateTime($coachData['dateOfBirth']));
                        $updated = true;
                    }
                    if ($coach->getNationality() !== $coachData['nationality']) {
                        $coach->setNationality($coachData['nationality']);
                        $updated = true;
                    }
                    if ($coach->getContractStart() !== $coachData['contract']['start']) {
                        $coach->setContractStart($coachData['contract']['start']);
                        $updated = true;
                    }
                    if ($coach->getContractUntil() !== $coachData['contract']['until']) {
                        $coach->setContractUntil($coachData['contract']['until']);
                        $updated = true;
                    }
                }
                
                foreach ($teamData['squad'] as $playerData) {
                    $player = $this->entityManager->find(Player::class, $playerData['id']);
                    if ($player) {
                        if ($player->getName() !== $playerData['name']) {
                            $player->setName($playerData['name']);
                            $updated = true;
                        }
                        if ($player->getPosition() !== $playerData['position']) {
                            $player->setPosition($playerData['position']);
                            $updated = true;
                        }
                        if ($player->getDate() != new DateTime($playerData['dateOfBirth'])) {
                            $player->setDate(new DateTime($playerData['dateOfBirth']));
                            $updated = true;
                        }
                        if ($player->getNationality() !== $playerData['nationality']) {
                            $player->setNationality($playerData['nationality']);
                            $updated = true;
                        }
                    } 
                }
                
                if ($updated) {
                    $this->entityManager->persist($team);
                }
            }
        }
        
        $this->entityManager->flush();
        $io->success('Updated data for '.count($teams).' teams.');
    }
}
