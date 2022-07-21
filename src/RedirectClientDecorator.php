<?php

declare(strict_types=1);

namespace Webclient\Extension\Redirect;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class RedirectClientDecorator implements ClientInterface
{
    private ClientInterface $client;
    private int $maxRedirects;

    public function __construct(ClientInterface $client, int $maxRedirects = 10)
    {
        $this->client = $client;
        $this->maxRedirects = max($maxRedirects, 0);
    }

    /**
     * @inheritDoc
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $exclude = [305, 306];
        $counter = 1;
        do {
            $response = $this->client->sendRequest($request);
            $status = $response->getStatusCode();
            $location = $response->getHeaderLine('Location');
            if ($status < 300 || $status > 399 || in_array($status, $exclude) || !$location) {
                return $response;
            }
            $counter++;
            if ($status === 303) {
                $request = $request->withMethod('GET');
            }
            $uri = $request->getUri();
            $url = ['user' => '', 'pass' => '', 'path' => '', 'query' => '', 'fragment' => ''];
            $url = array_replace($url, parse_url($location));
            $userInfo = $url['user'] . $url['pass'] ? ':' . $url['pass'] : '';
            if (array_key_exists('scheme', $url) && $url['scheme'] !== $uri->getScheme()) {
                $uri = $uri->withScheme($url['scheme']);
            }
            if ($uri->getUserInfo() !== $userInfo) {
                $uri = $uri->withUserInfo($userInfo);
            }
            if (array_key_exists('host', $url) && $url['host'] !== $uri->getHost()) {
                $uri = $uri->withHost($url['host']);
            }
            if (array_key_exists('port', $url) && $url['port'] !== $uri->getPort()) {
                $uri = $uri->withPort((int)$url['port']);
            }
            $uri = $uri
                ->withPath($url['path'])
                ->withQuery($url['query'])
                ->withFragment($url['fragment'])
            ;
            $request = $request->withUri($uri);
        } while ($counter <= $this->maxRedirects);
        return $response;
    }
}
