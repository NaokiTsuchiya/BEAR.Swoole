<?php

declare(strict_types=1);

namespace BEAR\Swoole;

use BEAR\Resource\ResourceObject;
use BEAR\Swoole\Exception\ResponseNotSeededException;
use PHPUnit\Framework\TestCase;

class ResponderTest extends TestCase
{
    /**
     * Invoking the transfer without a seeded response must fail loudly.
     *
     * Outside a coroutine (or in one where Responder::seed() was never called)
     * the response lookup returns null, so __invoke() must throw rather than
     * write to a missing response. The guard runs before $ro is touched, so a
     * bare ResourceObject is enough to exercise it.
     */
    public function testInvokeThrowsWhenResponseNotSeeded(): void
    {
        $responder = new Responder();
        $ro = new class extends ResourceObject {
        };

        $this->expectException(ResponseNotSeededException::class);

        $responder($ro, []);
    }
}
