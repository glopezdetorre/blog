<?php

namespace spec\Gorka\Blog;

use Gorka\Blog\Domain\Event\DomainEvent;

class TestDomainEvent implements DomainEvent {

    private $id;

    public function __construct($id) {
        $this->id = $id;
    }

    public function aggregateId()
    {
        return $this->id;
    }
}