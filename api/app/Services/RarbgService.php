<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class RarbgService
{
    private readonly Client $client;

    public function __construct(
        private readonly string $endpoint = 'https://torrentapi.org/pubapi_v2.php',
        private readonly string $app_id = 'torrent-getter'
    )
    {
        $this->client = new Client();
    }

    public function query(string $queryString, string $sortBy = 'seeders'): array
    {
        $params = [
            'mode' => 'search',
            'search_string' => $queryString,
            'sort' => $sortBy,
            'format' => 'json_extended',
            'ranked' => 0,
            'app_id' => $this->app_id,
            'token' => $this->generateToken()
        ];

        try {
            $req = $this->client->get($this->endpoint, ['query' => $params]);
        } catch (GuzzleException) {
            return [];
        }

        return json_decode($req->getBody(), true);
    }

    public function generateToken(): string
    {
        $params = [
            'get_token' => 'get_token',
            'app_id' => $this->app_id
        ];

        $req = $this->client->get($this->endpoint, ['query' => $params]);
        $res = json_decode($req->getBody());

        // RARBG has a 1req/1.5sec throttle
        sleep(1.5);

        return $res->token;
    }
}
