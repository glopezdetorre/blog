<?php

namespace Gorka\Blog\Domain\Model;

use Gorka\Blog\Domain\Event\DomainEvent;

class AggregateHistory extends EventHistory
{
    /**
     * @var AggregateId
     */
    private $id;

    public function __construct(AggregateId $id, EventHistory $events = null) {
        parent::__construct();

        $this->id = $id;
        if ($events !== null) {
            foreach ($events->events() as $event) {
                $this->add($event);
            }
        }
    }

    public function aggregateId()
    {
        return $this->id;
    }

    public function add(DomainEvent $event) {
        if ($event->aggregateId() != $this->id) {
            throw new \InvalidArgumentException('Event does not belong to this entity');
        }
        parent::add($event);
    }
}