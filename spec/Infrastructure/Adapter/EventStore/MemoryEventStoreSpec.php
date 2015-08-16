<?php

namespace spec\Gorka\Blog\Infrastructure\Adapter\EventStore;

use Gorka\Blog\Domain\Event\DomainEvent;
use Gorka\Blog\Domain\Model\AggregateHistory;
use Gorka\Blog\Domain\Model\AggregateId;
use Gorka\Blog\Infrastructure\Adapter\EventStore\MemoryEventStore;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/** @todo: this shouldn't be necessary */
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

class MemoryEventStoreSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MemoryEventStore::class);
    }

    function it_should_store_events(){

        $id1 = AggregateId::create('25769c6c-d34d-4bfe-ba98-e0ee856f3e7a');
        $id2 = AggregateId::create('25769c6c-d34d-4bfe-ba98-e0ee856f3e7b');

        $event1 = new TestDomainEvent($id1);
        $event2 = new TestDomainEvent($id1);
        $event3 = new TestDomainEvent($id2);

        $history1 = new AggregateHistory($id1, [$event1, $event2]);
        $history2 = new AggregateHistory($id2, [$event3]);

        $this->commit($history1);
        $this->commit($history2);

        $this->aggregateHistory($id1)->shouldBe($history1);
        $this->aggregateHistory($id2)->shouldBe($history2);
    }
}