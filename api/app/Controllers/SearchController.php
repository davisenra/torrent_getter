<?php

namespace App\Controllers;

use App\TorrentFetcher;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class SearchController
{
    public function search(Request $request, Response $response): Response
    {
        $payload = json_decode($request->getBody(), true);

        if (!in_array('search-string', array_keys($payload))) {
            $data = json_encode(['msg' => 'The search-string param is required']);
            $response->getBody()->write($data);

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        $fetcher = new TorrentFetcher($payload['search-string']);
        $fetcher->fetchAll();

        $torrents = $fetcher->getTorrentCollection();

        if (!count($torrents)) {
            $data = json_encode(['data' => []]);
            $response->getBody()->write($data);

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(204);
        }

        $data = json_encode(['data' => $torrents]);
        $response->getBody()->write($data);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}