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
    function it_is_initializable()
    {
        $this->shouldHaveType(MemoryEventStore::class);
    }

    function it_should_store_events(
        AggregateId $id1,
        AggregateId $id2,
        AggregateHistory $history1,
        AggregateHistory $history2,
        DomainEvent $de1,
        DomainEvent $de2,
        DomainEvent $de3
    ){
        $de1->aggregateId()->willReturn($id1);
        $de2->aggregateId()->willReturn($id1);
        $de3->aggregateId()->willReturn($id2);
        $history1->aggregateId()->willReturn($id1);
        $history2->aggregateId()->willReturn($id2);
        $history1->events()->willReturn([$de1, $de2]);
        $history2->events()->willReturn([$de3]);

        $this->commit($history1);
        $this->commit($history2);

        $this->aggregateHistory($id1)->events()->shouldBeLike([$de1, $de2]);
        $this->aggregateHistory($id2)->events()->shouldBeLike([$de3]);
    }
}