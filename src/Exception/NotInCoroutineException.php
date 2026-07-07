<?php

declare(strict_types=1);

namespace BEAR\Swoole\Exception;

use RuntimeException;
use Throwable;

final class NotInCoroutineException extends RuntimeException
{
    public function __construct(
        string $message = 'Expected to run inside a Swoole coroutine, but no coroutine context is available.',
        int $code = 0,
        Throwable|null $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
