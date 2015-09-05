<?php

namespace Gorka\Blog\Domain\Model;

use Assert\Assertion;
use Gorka\Blog\Domain\Event\DomainEvent;

/**
 * Class AggregateHistory
 */
class AggregateHistory
{
    /**
     * @var AggregateId
     */
    private $id;

    /**
     * @var DomainEvent[]
     */
    private $events = [];

    /**
     * @param AggregateId $id
     * @param null|DomainEvent|DomainEvent[] $events
     */
    public function __construct(AggregateId $id, $events = null)
    {
        $this->id = $id;

        if (null == $events) {
            $events = [];
        } else {
            if (!is_array($events)) {
                $events = [$events];
            }
        }

        $this->guardEventHistory($events);
        $this->events = $events;
    }

    /**
     * @return AggregateId
     */
    public function aggregateId()
    {
        return $this->id;
    }

    /**
     * @param DomainEvent $event
     */
    public function add(DomainEvent $event)
    {
        $this->guardEventHistory($event);
        $this->events[] = $event;
    }

    /**
     * @return DomainEvent[]
     */
    public function events()
    {
        return $this->events;
    }

    /**
     * @param DomainEvent[]|DomainEvent $events
     */
    private function guardEventHistory($events)
    {
        if (!is_array($events)) {
            $events = [$events];
        }
        Assertion::allIsInstanceOf($events, DomainEvent::class);
        foreach ($events as $event) {
            Assertion::eq($event->aggregateId(), $this->aggregateId());
        }
    }
}
