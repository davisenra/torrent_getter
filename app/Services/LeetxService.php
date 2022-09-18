<?php

namespace App\Services;

use Goutte\Client;

class LeetxService
{
    private string $baseURL;
    private string $url;

    public function __construct()
    {
        $this->baseURL = 'https://1337x.to';
        $this->url = 'https://1337x.to/search/';
    }

    public function query(string $queryString): array
    {
        $client = new Client();
        $html = $client->request('GET', $this->url . urlencode($queryString) . '/1/');
        $nodes = $html->filter('tbody > tr > td > a')->each(function ($node) {
            return $node->attr('href');
        });

        $linksToScrape = [];
        foreach ($nodes as $node) {
            if (strpos($node, 'torrent')) {
                $linksToScrape[] = $node;
            }
        }

        return $this->scrapLinks($linksToScrape);
    }

    public function scrapLinks(array $linksCollection): array
    {
        $torrents = [];

        $client = new Client();
        foreach ($linksCollection as $link) {
            $html = $client->request('GET', $this->baseURL . $link);
            $torrents[] = $this->extractTorrentInfo($html);
        }

        return $torrents;
    }

    public function extractTorrentInfo(object $html): array
    {
        $title = $html->filter('div.box-info > div > h1')->text();
        $magnet = $html->filter('div.box-info > div > div > ul > li > a')->eq(0)->attr('href');
        $seeders = $html->filter('ul.list > li > span.seeds')->text();
        $leechers = $html->filter('ul.list > li > span.leeches')->text();
        $size = $html->filter('div.box-info > div > div > ul.list > li > span')->eq(3)->text();

        return [
            'source' => '1337X',
            'title' => $title,
            'download' => $magnet,
            'seeders' => $seeders,
            'leechers' => $leechers,
            'size' => $size
        ];
    }
}