<?php

require 'vendor/autoload.php';

use App\Service\ApiService;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\HttpClient;

$logger = new NullLogger();
$client = HttpClient::create();
$apiService = new ApiService($client, $logger);

try {
    $teams = $apiService->getTeams();
    print_r($teams); // Add semicolon

    /*$matches = $apiService->getMatchesByTeam(1); // Replace 1 with a valid team ID
    print_r($matches); // Add semicolon*/
} catch (Exception $e) {
    echo 'Error: '.$e->getMessage();
}
