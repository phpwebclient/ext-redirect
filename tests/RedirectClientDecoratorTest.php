<?php

declare(strict_types=1);

namespace Tests\Webclient\Extension\Redirect;

use Nyholm\Psr7\Request;
use Stuff\Webclient\Extension\Redirect\Handler;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Webclient\Extension\Redirect\RedirectClientDecorator;
use Webclient\Fake\FakeHttpClient;

class RedirectClientDecoratorTest extends TestCase
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

        $client = new RedirectClientDecorator(new FakeHttpClient(new Handler()), $maxRedirects);

        $request = new Request('GET', 'http://localhost/?redirects=' . $needRedirects, ['Accept' => 'text/plain']);

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
