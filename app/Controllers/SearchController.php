<?php

namespace App\Controllers;

use App\Services\RarbgService;
use App\Services\YtsService;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class SearchController
{
    public function search(Request $request, Response $response): Response
    {
        $params = json_decode($request->getBody(), true);

        $ytsTorrents = $this->fetchYtsTorrents($params['search-string']);
        $rarbgTorrents = $this->fetchRarbgTorrents($params['search-string']);

        $torrents = array_merge($ytsTorrents, $rarbgTorrents);
        usort($torrents, fn($a, $b) => $b['seeders'] <=> $a['seeders']);

        if (!count($torrents)) {
            $response->getBody()->write('No results were found.');
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
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

    private function fetchRarbgTorrents(string $searchString): array
    {
        $rarbgService = new RarbgService();
        $data = $rarbgService->query($searchString);

        if (isset($data['error'])) {
            return [];
        }

        $torrents = [];

        foreach ($data['torrent_results'] as $torrent) {
            $torrents[] = [
                'source' => 'RARBG',
                'title' => $torrent['title'],
                'seeders' => $torrent['seeders'],
                'leechers' => $torrent['leechers'],
                'size' => $torrent['size'],
                'download' => $torrent['download']
            ];
        }

        return $torrents;
    }

    private function fetchYtsTorrents(string $searchString): array
    {
        $ytsService = new YtsService();
        $data = $ytsService->query($searchString);

        $torrents = [];
        $trackers = [
            '&tr=udp://open.demonii.com:1337/announce',
            '&tr=udp://tracker.openbittorrent.com:80',
            '&tr=udp://tracker.coppersurfer.tk:6969',
            '&tr=udp://glotorrents.pw:6969/announce',
            '&tr=udp://tracker.opentrackr.org:1337/announce',
            '&tr=udp://torrent.gresille.org:80/announce',
            '&tr=udp://p4p.arenabg.com:1337',
            '&tr=udp://tracker.leechers-paradise.org:6969'
        ];

        if (isset($data['data']['movies'])) {
            foreach ($data['data']['movies'] as $movie) {
                foreach ($movie['torrents'] as $torrent) {
                    /* Defining a better filename. */
                    $title = str_replace(' ', '.', $movie['title']) . '.' . $movie['year'] . '.' . $torrent['type'] . '.' . $torrent['quality'];

                    /* Manually stitching the magnet link since YTS API does not provide one. */
                    $magnet = 'magnet:?xt=urn:btih:' . $torrent['hash'] . '&dn=' . urlencode($movie['title']) . '&tr=';
                    $trackersString = join('&tr=', $trackers);

                    $torrents[] = [
                        'source' => 'YTS.MX',
                        'title' => $title,
                        'seeders' => $torrent['seeds'],
                        'leechers' => $torrent['peers'],
                        'size' => $torrent['size_bytes'],
                        'download' => $magnet . $trackersString
                    ];
                }
            }

            return $torrents;
        } else {
            return [];
        }
    }
}