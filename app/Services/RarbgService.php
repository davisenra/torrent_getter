<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;

class RarbgService
{
    private string $endpoint;
    private string $app_id;

    public function __construct()
    {
        $this->endpoint = 'https://torrentapi.org/pubapi_v2.php';
        $this->app_id = 'torrent-getter';
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

        $client = new Client();

        try {
            $req = $client->request('GET', $this->endpoint, ['query' => $params]);
            return json_decode($req->getBody(), true);
        } catch (ClientException|ServerException|GuzzleException $e) {
            if ($e->hasResponse()) {
                throw $e->getResponse()->getStatusCode();
            }
        }
    }

    public function generateToken(): string
    {
        $params = [
            'get_token' => 'get_token',
            'app_id' => $this->app_id
        ];

        $client = new Client();

        try {
            $req = $client->request('GET', $this->endpoint, ['query' => $params]);
            $res = json_decode($req->getBody());
            return $res->token;
        } catch (ClientException|ServerException|GuzzleException $e) {
            if ($e->hasResponse()) {
                throw $e->getResponse()->getStatusCode();
            }
        }
    }
}
