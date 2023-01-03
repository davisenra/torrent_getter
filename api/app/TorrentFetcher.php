<?php

namespace App;

use App\Helpers\Helper;
use App\Services\RarbgService;
use App\Services\YtsService;

class TorrentFetcher
{
    private array $torrentCollection = [];

    public function __construct(
        private readonly string $searchString
    ) {}

    public function getTorrentCollection(): array
    {
        return $this->torrentCollection;
    }

    public function fetchAll(): void
    {
        $services = [
            'yts' => $this->fetchYtsTorrents(),
            'rarbg' => $this->fetchRarbgTorrents()
        ];

        foreach ($services as $service) {
            if (empty($service)) {
                continue;
            }

            $this->torrentCollection = array_merge(
                $this->torrentCollection,
                $service
            );
        }

        usort($this->torrentCollection,
            fn($a, $b) => $b['seeders'] <=> $a['seeders']
        );
    }

    private function fetchRarbgTorrents(): array
    {
        $rarbgService = new RarbgService();
        $data = $rarbgService->query($this->searchString);

        if (!isset($data['torrent_results'])) {
            return [];
        }

        $torrents = [];

        foreach ($data['torrent_results'] as $torrent) {
            $torrents[] = [
                'source' => 'RARBG',
                'title' => $torrent['title'],
                'seeders' => $torrent['seeders'],
                'leechers' => $torrent['leechers'],
                'size' => Helper::formatBytes($torrent['size']),
                'magnet' => $torrent['download']
            ];
        }

        return $torrents;
    }

    private function fetchYtsTorrents(): array
    {
        $ytsService = new YtsService();
        $data = $ytsService->query($this->searchString);

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
                    // Defining a better filename
                    $title = join('.', [
                        str_replace(' ', '.', $movie['title']),
                        $movie['year'],
                        $torrent['type'],
                        $torrent['quality']
                    ]);

                    // Manually stitching the magnet link since YTS API does not provide one
                    $magnet = 'magnet:?xt=urn:btih:' . $torrent['hash'] . '&dn='
                        . urlencode($movie['title']) . '&tr=';

                    $trackersString = join('&tr=', $trackers);

                    $torrents[] = [
                        'source' => 'YTS.MX',
                        'title' => $title,
                        'seeders' => $torrent['seeds'],
                        'leechers' => $torrent['peers'],
                        'size' => Helper::formatBytes($torrent['size_bytes']),
                        'magnet' => $magnet . $trackersString
                    ];
                }
            }

            return $torrents;
        }

        return [];
    }
}