<?php

namespace App\Controllers;

use App\Services\RarbgService;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class SearchController
{
    public function search(Request $request, Response $response): Response
    {
        $params = json_decode($request->getBody(), true);

        $rarbgService = new RarbgService();
        $torrents = $rarbgService->query($params['search-string']);

        if (isset($torrents->error)) {
            $data = json_encode([
                'success' => false,
                'error' => $torrents->error
            ]);

            $response->getBody()->write($data);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(204);
        }

        $data = json_encode([
            'success' => true,
            'data' => $torrents
        ]);

        $response->getBody()->write($data);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}