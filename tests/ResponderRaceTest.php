<?php

declare(strict_types=1);

namespace BEAR\Swoole;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;
use PHPUnit\Framework\TestCase;

use function json_decode;

use const JSON_THROW_ON_ERROR;

class ResponderRaceTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = new Client([
            'base_uri' => 'http://127.0.0.1:8088',
            'http_errors' => false,
            'timeout' => 10,
        ]);
    }

    /**
     * Concurrent requests must not mix responses between clients.
     *
     * The /slow resource embeds a sub-resource whose onGet performs
     * coroutine-hooked I/O, so rendering yields inside toString() while the
     * response is being transferred. Each client must still receive the body
     * for its own request.
     */
    public function testConcurrentRequestsDoNotMixResponses(): void
    {
        // Warm up the route first: cold-start class loading blocks the worker
        // without yielding, which masks the coroutine interleaving under test.
        $this->client->get('/slow?id=warmup');

        $promiseA = $this->client->getAsync('/slow?id=A');
        $promiseB = $this->client->getAsync('/slow?id=B');
        [$responseA, $responseB] = Utils::unwrap([$promiseA, $promiseB]);

        $this->assertSame(200, $responseA->getStatusCode());
        $this->assertSame(200, $responseB->getStatusCode());
        /** @var array{id: string, part: array{done: bool}} $bodyA */
        $bodyA = json_decode((string) $responseA->getBody(), true, 512, JSON_THROW_ON_ERROR);
        /** @var array{id: string, part: array{done: bool}} $bodyB */
        $bodyB = json_decode((string) $responseB->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('A', $bodyA['id']);
        $this->assertSame('B', $bodyB['id']);
        // The lazy embed is what makes rendering yield mid-transfer; if it stops
        // rendering, this test no longer exercises the race it guards against.
        $this->assertSame(['done' => true], $bodyA['part']);
        $this->assertSame(['done' => true], $bodyB['part']);
    }
}
