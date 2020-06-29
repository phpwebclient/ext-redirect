<?php

declare(strict_types=1);

namespace Tests\Webclient\Extension\Redirect;

use Stuff\Webclient\Extension\Redirect\Handler;
use Nyholm\Psr7\Request;
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

        $request = new Request('GET', 'http://localhost?redirects=' . $needRedirects, ['Accept' => 'text/plain']);

        $response = $client->sendRequest($request);

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
