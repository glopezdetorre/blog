<?php

namespace spec\Gorka\Blog\Domain\Model;

use Gorka\Blog\Domain\Event\DomainEvent;
use Gorka\Blog\Domain\Model\AggregateHistory;
use Gorka\Blog\Domain\Model\AggregateId;
use Gorka\Blog\Domain\Model\EventHistory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AggregateHistorySpec extends ObjectBehavior
{
    function let(AggregateId $id, EventHistory $events) {
        $events->getIterator()->willReturn(new \ArrayIterator([]));
        $this->beConstructedWith($id, $events);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AggregateHistory::class);
    }

    function it_should_allow_retrieve_aggregate_id(AggregateId $id)
    {
        $this->aggregateId()->shouldBe($id);
    }

    function it_should_allow_retrieve_event_history(EventHistory $events)
    {
        $this->events()->shouldBe($events);
    }

    function it_should_allow_adding_events_from_same_aggregate(AggregateId $id, DomainEvent $event)
    {
        $this->beConstructedWith($id);
        $event->aggregateId()->willReturn($id);
        $this->add($event);
        $this->events()->shouldBe([$event]);
    }

    function it_should_not_allow_adding_event_from_other_agregate(AggregateId $id2, DomainEvent $event)
    {
        $event->aggregateId()->willReturn($id2);
        $this->shouldThrow(\InvalidArgumentException::class)->during('add', [$event]);
    }
}
