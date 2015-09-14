<?php

namespace Gorka\Blog\Infrastructure\Service;

use Gorka\Blog\Domain\Service\IdGenerator;
use Rhumsaa\Uuid\Uuid;

class UuidGenerator implements IdGenerator
{
    public function id()
    {
        return Uuid::uuid4()->toString();
    }
}
