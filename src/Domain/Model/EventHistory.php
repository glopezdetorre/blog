<?php

namespace Gorka\Blog\Domain\Model;

use Gorka\Blog\Domain\Event\DomainEvent;

class EventHistory
{
    /**
     * @var DomainEvent[]
     */
    private $events;

    public function __construct()
    {
        $this->events = [];
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
}