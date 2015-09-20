<?php

namespace Gorka\Blog\Domain\Event;

use Gorka\Blog\Domain\Model\AggregateId;
use Gorka\Blog\Domain\Model\DomainMessage;

interface DomainEvent extends DomainMessage
{
    /**
     * @return AggregateId
     */
    public function aggregateId();
}
