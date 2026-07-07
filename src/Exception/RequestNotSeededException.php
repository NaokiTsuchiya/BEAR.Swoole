<?php

declare(strict_types=1);

namespace BEAR\Swoole\Exception;

use RuntimeException;
use Throwable;

final class RequestNotSeededException extends RuntimeException
{
    public function __construct(
        string $message = 'The Swoole request was not seeded into the coroutine context; call SwooleRequestProvider::seed() before use.',
        int $code = 0,
        Throwable|null $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
