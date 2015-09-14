<?php

namespace Gorka\Blog\Infrastructure\Data\EventStore;

use Gorka\Blog\Domain\Event\DomainEvent;
use Gorka\Blog\Domain\Model\AggregateHistory;
use Gorka\Blog\Domain\Model\AggregateId;
use Gorka\Blog\Domain\Model\EventStore;
use Gorka\Blog\Infrastructure\Exception\Data\DataNotFoundException;

class MemoryEventStore implements EventStore
{

    /**
     * @var DomainEvent[]
     */
    private $events = [];

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
     * @return \Gorka\Blog\Domain\Event\DomainEvent[]
     * @throws DataNotFoundException
     */
    public function events(AggregateId $id)
    {
        $events = array_filter(
            $this->events,
            function ($event) use ($id) {
                return $event->aggregateId() == $id;
            }
        );

        if (count($events) == 0) {
            throw new DataNotFoundException();
        }

        return $events;
    }
}
