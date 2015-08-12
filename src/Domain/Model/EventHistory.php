<?php

namespace Gorka\Blog\Domain\Model;

use Assert\Assertion;
use Gorka\Blog\Domain\Event\DomainEvent;

class EventHistory implements \IteratorAggregate
{
    /**
     * @var DomainEvent[]
     */
    private $events;

    public function __construct($events = null)
    {
        if ($events === null) {
            $events = [];
        } elseif ($events instanceof DomainEvent) {
            $events = [$events];
        }

        $this->guardEventArray($events);
        $this->events = $events;
    }

    public function add(DomainEvent $event)
    {
        $this->events[] = $event;
    }

    public function events()
    {
        return $this->events;
    }

    public function clear()
    {
        $this->events = [];
    }

    /**
     * @param DomainEvent[] $events
     */
    private function guardEventArray($events)
    {
        Assertion::allIsInstanceOf($events, DomainEvent::class, 'All elements in the array should be DomainEvents');
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->events());
    }
}