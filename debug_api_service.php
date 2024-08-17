<?php

require 'vendor/autoload.php';

use App\Service\ApiService;
use Symfony\Component\HttpClient\HttpClient;

$client = HttpClient::create();
$apiService = new ApiService($client);

try {
    /*$standing = $apiService->getStanding();
    print_r($standing); */

    $matches = $apiService->getMatchesDed();
    print_r($matches);
} catch (Exception $e) {
    echo 'Error: '.$e->getMessage();
}
