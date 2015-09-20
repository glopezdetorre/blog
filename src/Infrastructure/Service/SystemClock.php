<?php

namespace Gorka\Blog\Infrastructure\Service;

use Gorka\Blog\Domain\Service\SystemClock as SystemClockInterface;

class SystemClock implements SystemClockInterface
{
    /**
     * @return \DateTimeImmutable
     */
    public function now()
    {
        return new \DateTimeImmutable();
    }
}
