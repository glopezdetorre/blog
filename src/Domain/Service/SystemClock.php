<?php

namespace Gorka\Blog\Domain\Service;

interface SystemClock
{
    /**
     * @param \DateTimeZone $dtz
     * @return \DateTimeImmutable
     */
    public function now(\DateTimeZone $dtz = null);
}
