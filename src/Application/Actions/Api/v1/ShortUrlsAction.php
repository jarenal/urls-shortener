<?php

declare(strict_types=1);

namespace App\Application\Actions\Api\v1;

use App\Application\Actions\Action;
use App\Infrastructure\Services\UrlShortenerService;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class ShortUrlsAction extends Action
{
    private UrlShortenerService $urlShortenerService;

    public function __construct(LoggerInterface $logger, UrlShortenerService $urlShortenerService)
    {
        parent::__construct($logger);
        $this->urlShortenerService = $urlShortenerService;
    }

    /**
     * @throws GuzzleException
     */
    protected function action(): ResponseInterface
    {
        $parsedBody = $this->request->getParsedBody();
        $shortUrl = ($this->urlShortenerService)($parsedBody['url']);
        return $this->respondWithData(['url' => $shortUrl]);
    }
}
