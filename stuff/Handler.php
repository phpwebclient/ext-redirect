<?php

declare(strict_types=1);

namespace Stuff\Webclient\Extension\Redirect;

use Pluf\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Handler implements RequestHandlerInterface
{

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $status = 200;
        $query = $request->getQueryParams();
        $headers = [
            'Content-Type' => 'text/plain',
        ];
        $visit = 0;
        if (array_key_exists('visit', $query) && (int)$query['visit'] > 0) {
            $visit = (int)$query['visit'];
        }
        $visit++;
        $redirects = 0;
        if (array_key_exists('redirects', $query) && (int)$query['redirects'] > 0) {
            $redirects = (int)$query['redirects'] - 1;
        }
        if ($redirects > 0) {
            $get = http_build_query([
                'redirects' => $redirects,
                'visit' => $visit,
            ]);
            $headers['Location'] = $request->getUri()->withQuery($get)->__toString();
            $status = 302;
        }
        $response = new Response($status);
        foreach ($headers as $header => $value) {
            $response = $response->withHeader($header, $value);
        }
        $response->getBody()->write((string)$visit);
        return $response->withProtocolVersion('1.1');
    }
}
