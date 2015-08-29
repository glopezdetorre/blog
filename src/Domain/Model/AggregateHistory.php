<?php

namespace Gorka\Blog\Domain\Model;

use Gorka\Blog\Domain\Event\DomainEvent;

class AggregateHistory
{
    /**
     * @var AggregateId
     */
    private $id;

    /**
     * @var EventHistory
     */
    private $eventHistory;

    public function __construct(AggregateId $id, $events = null)
    {
        $this->id = $id;
        $this->eventHistory = new EventHistory($events);
    }

    public function aggregateId()
    {
        return $this->id;
    }

    public function add(DomainEvent $event)
    {
        if ($event->aggregateId() != $this->id) {
            throw new \InvalidArgumentException('Event does not belong to this entity');
        }
        $this->eventHistory->add($event);
    }

    public function events()
    {
        return $this->eventHistory->events();
    }
}
