<?php

namespace spec\Gorka\Blog\Infrastructure\Data\EventStore;

use Gorka\Blog\Domain\Event\DomainEvent;
use Gorka\Blog\Domain\Model\AggregateHistory;
use Gorka\Blog\Domain\Model\AggregateId;
use Gorka\Blog\Domain\Model\EventStore;
use Gorka\Blog\Infrastructure\Data\EventStore\MemoryEventStore;
use Gorka\Blog\Infrastructure\Exception\Data\DataNotFoundException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/** @mixin MemoryEventStore */
class MemoryEventStoreSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MemoryEventStore::class);
    }

    function it_is_an_event_store()
    {
        $this->shouldBeAnInstanceOf(EventStore::class);
    }


    function it_should_allow_storing_events(AggregateId $id, DomainEvent $domainEvent, AggregateHistory $history)
    {
        $domainEvent->aggregateId()->willReturn($id);
        $history->aggregateId()->willReturn($id);
        $history->events()->willReturn([$domainEvent]);

        $this->commit($history);
        $this->events($id)->shouldBe([$domainEvent]);
    }
    function it_should_filter_events_by_given_aggregate_id(
        AggregateId $id,
        DomainEvent $domainEvent,
        AggregateHistory $history
    ) {
        $domainEvent->aggregateId()->willReturn($id);
        $history->aggregateId()->willReturn($id);
        $history->events()->willReturn([$domainEvent]);

        $this->commit($history);
        $this->events($id)->shouldBe([$domainEvent]);
    }

    function it_should_throw_exception_on_aggregate_history_not_found(AggregateId $id) {
        $this->shouldThrow(DataNotFoundException::class)->during('events', [$id]);
    }
}
