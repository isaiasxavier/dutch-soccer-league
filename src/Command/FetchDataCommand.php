<?php

// src/Command/FetchDataCommand.php

namespace App\Command;

use App\Service\ApiService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FetchDataCommand extends Command
{
    protected static $defaultName = 'app:fetch-data';
    private $apiService;

    public function __construct(ApiService $apiService)
    {
        parent::__construct();
        $this->apiService = $apiService;
    }

    protected function configure()
    {
        $this
            ->setDescription('Fetch data from external API');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $limit = 10;
        $offset = 0;
        $teams = $this->apiService->getTeams($limit, $offset);
        $io->success('Fetched '.count($teams).' teams.');

        foreach ($teams as $team) {
            $matches = $this->apiService->getMatchesByTeam($team['id'], $limit, $offset);
            $io->success('Fetched '.count($matches).' matches for team '.$team['name']);
        }

        return Command::SUCCESS;
    }
}
