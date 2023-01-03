<?php

namespace App\Services;

use GuzzleHttp\Client;

class YtsService
{
    private Client $client;

    public function __construct(
        public readonly string $endpoint = 'https://yts.mx/api/v2/list_movies.json'
    )
    {
        $this->client = new Client();
    }

    public function query(string $queryString, string $sortBy = 'seeds'): array
    {
        $params = [
            'query_term' => $queryString,
            'sort_by' => $sortBy,
            'limit' => '20',
        ];

        $url = $this->endpoint . '?' . http_build_query($params, '&', '&');

        $req = $this->client->get($url);

        return json_decode($req->getBody(), true);
    }
}