<?php

namespace Gorka\Blog\Domain\Model;

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
     * @return AggregateHistory
     */
    public function recordedEvents();
}
