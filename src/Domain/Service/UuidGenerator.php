<?php

namespace Gorka\Blog\Domain\Service;

use Rhumsaa\Uuid\Uuid;

class UuidGenerator
{
    public function id()
    {
        return Uuid::uuid4()->toString();
    }
}
