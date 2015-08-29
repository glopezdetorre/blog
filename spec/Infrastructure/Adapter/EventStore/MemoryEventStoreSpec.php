<?php

namespace spec\Gorka\Blog\Infrastructure\Adapter\EventStore;

use Gorka\Blog\Domain\Event\DomainEvent;
use Gorka\Blog\Domain\Model\AggregateHistory;
use Gorka\Blog\Domain\Model\AggregateId;
use Gorka\Blog\Infrastructure\Adapter\EventStore\MemoryEventStore;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MemoryEventStoreSpec extends ObjectBehavior
{
    function let(
        DomainEvent $event1,
        AggregateId $id1,
        AggregateId $id2,
        AggregateHistory $history1,
        AggregateHistory $history2
    ) {
        $history1->aggregateId()->willReturn($id1);
        $history2->aggregateId()->willReturn($id2);
        $event1->aggregateId()->willReturn($id1);
        $history1->events()->willReturn([$event1]);
        $history2->events()->willReturn([]);
        $this->beConstructedWith();
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MemoryEventStore::class);
    }

    function it_should_store_events(
        DomainEvent $event1,
        AggregateId $id1,
        AggregateId $id2,
        AggregateHistory $history1,
        AggregateHistory $history2
    ){
        $this->commit($history1);
        $this->commit($history2);

        $this->aggregateHistory($id1)->aggregateId()->shouldBeLike($id1);
        $this->aggregateHistory($id1)->events()->shouldBeLike([$event1]);

        $this->aggregateHistory($id2)->aggregateId()->shouldBeLike($id2);
        $this->aggregateHistory($id2)->events()->shouldBeLike([]);
    }
}