<?php

namespace Gorka\Blog\Domain\Model;

use Gorka\Blog\Domain\Event\DomainEvent;

/**
 * Interface EventSourcedAggregate
 */
interface EventSourcedAggregate
{
    /**
     * @return AggregateId
     */
    public function id();

    /**
     * @param AggregateHistory $history
     * @return EventSourcedAggregate
     */
    public static function reconstituteFromEvents(AggregateHistory $history);

    /**
     * @return DomainEvent[]
     */
    public function recordedEvents();
}
