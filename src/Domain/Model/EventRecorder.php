<?php

namespace Gorka\Blog\Domain\Model;

use Gorka\Blog\Domain\Event\DomainEvent;

/**
 * Class EventRecorder
 */
abstract class EventRecorder implements EventSourcedAggregate
{
    /**
     * @var DomainEvent[]
     */
    private $events = [];

    /**
     * @return \Gorka\Blog\Domain\Event\DomainEvent[]
     */
    public function recordedEvents()
    {
        return $this->events;
    }

    /**
     * @param DomainEvent $event
     */
    protected function recordThat(DomainEvent $event)
    {
        $this->events[] = $event;
        $this->apply($event);
    }

    /**
     * @param DomainEvent $event
     */
    abstract protected function apply(DomainEvent $event);
}
