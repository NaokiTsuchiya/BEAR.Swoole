<?php

declare(strict_types=1);

namespace BEAR\Swoole;

use ArrayObject;
use BEAR\Resource\ResourceObject;
use BEAR\Sunday\Extension\Transfer\TransferInterface;
use BEAR\Swoole\Exception\NotInCoroutineException;
use BEAR\Swoole\Exception\ResponseNotSeededException;
use Swoole\Coroutine;
use Swoole\Http\Response;

/**
 * The Swoole response travels via coroutine context (like the request) so that
 * this instance can be shared across concurrent request coroutines.
 *
 * @codeCoverageIgnore Swoole server context only
 */
final readonly class Responder implements TransferInterface
{
    /**
     * Seed the coroutine context with the raw Swoole response.
     */
    public static function seed(Response $response): void
    {
        /** @var ArrayObject<string, mixed>|null $context */
        $context = Coroutine::getContext();
        if ($context === null) {
            throw new NotInCoroutineException(); // @codeCoverageIgnore
        }

        $context[Response::class] = $response;
    }

    public function __invoke(ResourceObject $ro, array $server): void
    {
        unset($server);
        $response = CoroutineContextFinder::find(Response::class, Response::class);
        if ($response === null) {
            throw new ResponseNotSeededException();
        }

        $ro->toString();
        foreach ($ro->headers as $key => $value) {
            $response->header($key, (string) $value);
        }

        $response->status($ro->code);
        $response->end($ro->view);
    }
}
