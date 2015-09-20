<?php

namespace Gorka\Blog\Domain\Service;

interface SystemClock
{
    /**
     * @return \DateTimeImmutable
     */
    public function now();
}
