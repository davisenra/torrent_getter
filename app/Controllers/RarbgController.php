<?php

namespace App\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class RarbgController
{
    private string $endpoint;
    private string $app_id;

    public function __construct()
    {
        $this->endpoint = 'https://torrentapi.org/pubapi_v2.php';
        $this->app_id = 'torrent-getter';
    }

    public function query(string $queryString, string $sortBy = 'seeders'): object
    {
        $client = new Client();

        $params = [
            'mode' => 'search',
            'search_string' => $queryString,
            'sort' => $sortBy,
            'format' => 'json_extended',
            'ranked' => 0,
            'app_id' => $this->app_id,
            'token' => $this->getToken()
        ];

        sleep(1);

        try {
            $req = $client->request('GET', $this->endpoint, ['query' => $params]);
            $res = json_decode($req->getBody());
        } catch (ClientException | ServerException $e) {
            if ($e->hasResponse()) {
                throw $e->getResponse()->getStatusCode();
            }
        }

        return $res;
    }

    public function getToken(): string
    {
        $client = new Client();

        $params = [
            'get_token' => 'get_token',
            'app_id' => $this->app_id
        ];

        try {
            $req = $client->request('GET', $this->endpoint, ['query' => $params]);
            $res = json_decode($req->getBody());
        } catch (ClientException | ServerException $e) {
            if ($e->hasResponse()) {
                throw $e->getResponse()->getStatusCode();
            }
        }

        return $res->token;
    }
}
