<?php

declare(strict_types=1);

namespace App\Infrastructure\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class UrlShortenerService
{
    /**
     * @throws GuzzleException
     */
    public function __invoke(string $url): string
    {
        $client = new Client([
            'base_uri' => 'https://tinyurl.com/',
            'timeout' => 4.0,
        ]);

        $response = $client->request('GET', 'api-create.php', [
            'query' => ['url' => $url],
        ]);

        return (string)$response->getBody();
    }
}
