<?php

declare(strict_types=1);

namespace Tests\Webclient\Extension\Redirect;

use Pluf\Http\Headers;
use Pluf\Http\Stream;
use Pluf\Http\Uri;
use Stuff\Webclient\Extension\Redirect\Handler;
use Pluf\Http\Request;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Webclient\Extension\Redirect\Client;
use Webclient\Fake\Client as FakeClient;

class RedirectClientTest extends TestCase
{

    /**
     * @param int $maxRedirects
     * @param int $needRedirects
     * @param int $expectRedirects
     *
     * @dataProvider provideRedirects
     *
     * @throws ClientExceptionInterface
     */
    public function testRedirects(int $maxRedirects, int $needRedirects, int $expectRedirects)
    {

        $client = new Client(new FakeClient(new Handler()), $maxRedirects);

        $headers = new Headers(['Accept' => 'text/plain']);
        $uri = new Uri('http', 'localhost', 80, '/', 'redirects=' . $needRedirects);
        $resource = fopen('php://temp', 'w+');
        $body = new Stream($resource);
        $request = new Request('GET', $uri, $headers, [], [], $body);

        $response = $client->sendRequest($request);
        fclose($resource);
        $this->assertSame($expectRedirects, (int)$response->getBody()->__toString());
    }

    public function provideRedirects(): array
    {
        return [
            [0, 1, 1],
            [0, 10, 1],
            [10, 5, 5],
            [10, 10, 10],
            [9, 10, 9],
        ];
    }
}
