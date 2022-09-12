<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;

class YtsService
{
    private string $endpoint;

    public function __construct()
    {
        $this->endpoint = 'https://yts.mx/api/v2/list_movies.json';
    }

    public function query(string $queryString, string $sortBy = 'seeds'): array
    {
        $params = [
            'query_term' => $queryString,
            'sort_by' => $sortBy,
            'limit' => '20',
        ];

        $url = $this->endpoint . '?' . http_build_query($params, '&', '&');

        $client = new Client();

        try {
            $req = $client->request('GET', $url);
            return json_decode($req->getBody(), true);
        } catch (ClientException|ServerException|GuzzleException $e) {
            if ($e->hasResponse()) {
                throw $e->getResponse()->getStatusCode();
            }
        }
    }
}