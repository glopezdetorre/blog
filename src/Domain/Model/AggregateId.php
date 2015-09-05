<?php

namespace Gorka\Blog\Domain\Model;

/**
 * Interface AggregateId
 */
interface AggregateId
{
    /**
     * @return mixed
     */
    public function id();

    /**
     * @return string
     */
    public function __toString();
}
