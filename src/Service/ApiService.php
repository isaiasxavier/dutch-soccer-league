<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiService
{
    private HttpClientInterface $client;
    private string $apiUrl = 'https://api.football-data.org/v4';
    private string $authToken = '5be239f512d74fd991afb3ab502916e4';

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }
    
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getTeams(): array
    {
        $response = $this->client->request('GET', $this->apiUrl.'/competitions/DED/teams', [
            'headers' => [
                'X-Auth-Token' => $this->authToken,
            ],
        ]);

        $data = $response->toArray();

        return $data['teams'];
    }
    
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getMatchesDed(): array
    {
        $response = $this->client->request('GET', $this->apiUrl.'/competitions/DED/matches', [
            'headers' => [
                'X-Auth-Token' => $this->authToken,
            ],
        ]);

        return $response->toArray();
    }
    
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getStanding(): array
    {
        $response = $this->client->request('GET', $this->apiUrl.'/competitions/DED/standings', [
            'headers' => [
                'X-Auth-Token' => $this->authToken,
            ],
        ]);

        return $response->toArray();
    }
}
