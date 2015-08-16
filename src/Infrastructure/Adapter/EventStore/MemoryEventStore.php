<?php

namespace Gorka\Blog\Infrastructure\Adapter\EventStore;

use Gorka\Blog\Domain\Event\DomainEvent;
use Gorka\Blog\Domain\Event\Post\PostWasCreated;
use Gorka\Blog\Domain\Model\AggregateHistory;
use Gorka\Blog\Domain\Model\AggregateId;
use Gorka\Blog\Domain\Model\Post\PostId;

class MemoryEventStore
{

    /**
     * @var DomainEvent[]
     */
    private $events;

    public function __construct()
    {
        $this->events = [];
    }

    public function commit(AggregateHistory $history)
    {
        foreach ($history->events() as $event) {
            $this->events[] = $event;
        }
    }

    public function aggregateHistory(AggregateId $id)
    {
        $events = array_filter(
            $this->events,
            function ($event) use ($id) {
                return $event->aggregateId() == $id;
            }
        );

        return new AggregateHistory($id, $events);
    }
}