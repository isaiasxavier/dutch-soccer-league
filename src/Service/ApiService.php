<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiService
{
    private $client;
    private $apiUrl = 'https://api.football-data.org/v4';
    private $authToken = '5be239f512d74fd991afb3ab502916e4';

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getTeams(int $limit = 10, int $offset = 0): array
    {
        $response = $this->client->request('GET', $this->apiUrl.'/competitions/DED/teams', [
            'headers' => [
                'X-Auth-Token' => $this->authToken,
            ],
        ]);

        $data = $response->toArray();

        return $data['teams'];
    }

    public function getTeamById($teamId): array
    {
        $response = $this->client->request('GET', $this->apiUrl.'/teams/'.$teamId, [
            'headers' => [
                'X-Auth-Token' => $this->authToken,
            ],
        ]);

        return $response->toArray();
    }

    public function getMatchesByTeam($teamId, int $limit = 10, int $offset = 0): array
    {
        $response = $this->client->request('GET', $this->apiUrl.'/teams/'.$teamId.'/matches', [
            'headers' => [
                'X-Auth-Token' => $this->authToken,
            ],
            'query' => [
                'limit' => $limit,
                'offset' => $offset,
            ],
        ]);

        return $response->toArray();
    }
}
