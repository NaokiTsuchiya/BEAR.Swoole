<?php

declare(strict_types=1);

namespace BEAR\Swoole\Exception;

use RuntimeException;
use Throwable;

final class ResponseNotSeededException extends RuntimeException
{
    public function __construct(
        string $message = 'The Swoole response was not seeded into the coroutine context; call Responder::seed() before transferring.',
        int $code = 0,
        Throwable|null $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
