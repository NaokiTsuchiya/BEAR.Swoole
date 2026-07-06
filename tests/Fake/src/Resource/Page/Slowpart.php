<?php

declare(strict_types=1);

namespace BEAR\Skeleton\Resource\Page;

use BEAR\Resource\ResourceObject;

use function usleep;

class Slowpart extends ResourceObject
{
    public function onGet(): static
    {
        // Coroutine-hooked sleep: yields during render-time embed evaluation
        usleep(300_000);
        $this->body = ['done' => true];

        return $this;
    }
}
