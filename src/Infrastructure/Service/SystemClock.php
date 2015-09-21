<?php

namespace Gorka\Blog\Infrastructure\Service;

use Gorka\Blog\Domain\Service\SystemClock as SystemClockInterface;

class SystemClock implements SystemClockInterface
{
    /**
     * @param \DateTimeZone $dtz
     * @return \DateTimeImmutable
     */
    public function now(\DateTimeZone $dtz = null)
    {
        return new \DateTimeImmutable('now', $dtz);
    }
}
