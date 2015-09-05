<?php

namespace Gorka\Blog\Infrastructure\Data\EventStore;

use Gorka\Blog\Domain\Event\DomainEvent;
use Gorka\Blog\Domain\Model\AggregateHistory;
use Gorka\Blog\Domain\Model\AggregateId;
use Gorka\Blog\Domain\Model\EventStore;

class MemoryEventStore implements EventStore
{

    /**
     * @var DomainEvent[]
     */
    private $events;

    /**
     * @param AggregateHistory $history
     */
    public function commit(AggregateHistory $history)
    {
        foreach ($history->events() as $event) {
            $this->events[] = $event;
        }
    }

    /**
     * @param AggregateId $id
     * @return DomainEvent[]
     */
    public function events(AggregateId $id)
    {
        return array_filter(
            $this->events,
            function ($event) use ($id) {
                return $event->aggregateId() == $id;
            }
        );
    }
}
