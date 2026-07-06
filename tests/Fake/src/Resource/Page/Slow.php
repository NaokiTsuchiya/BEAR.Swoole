<?php

declare(strict_types=1);

namespace BEAR\Skeleton\Resource\Page;

use BEAR\Resource\Annotation\Embed;
use BEAR\Resource\ResourceObject;

use function assert;
use function is_array;

class Slow extends ResourceObject
{
    #[Embed(rel: 'part', src: 'page://self/slowpart')]
    public function onGet(string $id): static
    {
        assert(is_array($this->body)); // seeded with 'part' by #[Embed] before this method runs
        $this->body['id'] = $id;

        return $this;
    }
}
