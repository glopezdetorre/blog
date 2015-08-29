<?php

namespace Gorka\Blog\Domain\Event;

use Gorka\Blog\Domain\Model\AggregateId;

interface DomainEvent
{
    /**
     * @return AggregateId
     */
    public function aggregateId();
}
