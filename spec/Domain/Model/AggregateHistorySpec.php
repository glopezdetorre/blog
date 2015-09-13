<?php

namespace spec\Gorka\Blog\Domain\Model;

use Gorka\Blog\Domain\Event\DomainEvent;
use Gorka\Blog\Domain\Model\AggregateHistory;
use Gorka\Blog\Domain\Model\AggregateId;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AggregateHistorySpec extends ObjectBehavior
{
    function let(AggregateId $id, DomainEvent $domainEvent, DomainEvent $domainEvent2) {
        $domainEvent->aggregateId()->willReturn($id);
        $domainEvent2->aggregateId()->willReturn($id);
        $this->beConstructedWith($id, [$domainEvent, $domainEvent2]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AggregateHistory::class);
    }

    function it_should_be_initializable_with_empty_events(AggregateId $id)
    {
        $this->beConstructedWith($id);
        $this->events()->shouldBe([]);
    }

    function it_should_be_initializable_with_single_event(AggregateId $id, DomainEvent $domainEvent)
    {
        $this->beConstructedWith($id, $domainEvent);
        $this->events()->shouldBe([$domainEvent]);
    }

    function it_should_not_allow_initialization_with_events_from_different_aggregate(
        AggregateId $id,
        AggregateId $id2,
        DomainEvent $domainEvent3
    ) {
        $domainEvent3->aggregateId()->willReturn($id2);
        $this->shouldThrow(\InvalidArgumentException::class)->during('__construct', [$id, $domainEvent3]);
    }

    function it_should_allow_retrieving_aggregate_id(AggregateId $id)
    {
        $this->aggregateId()->shouldBe($id);
    }

    function it_should_allow_retrieving_events(DomainEvent $domainEvent, DomainEvent $domainEvent2)
    {
        $this->events()->shouldBe([$domainEvent, $domainEvent2]);
    }

    function it_should_allow_adding_events(
        AggregateId $id,
        DomainEvent $domainEvent,
        DomainEvent $domainEvent2,
        DomainEvent $domainEvent3
    ) {
        $domainEvent3->aggregateId()->willReturn($id);
        $this->add($domainEvent3);
        $this->events()->shouldBe([$domainEvent, $domainEvent2, $domainEvent3]);
    }

    function it_should_not_allow_adding_events_from_different_aggregate(AggregateId $id2, DomainEvent $domainEvent3)
    {
        $domainEvent3->aggregateId()->willReturn($id2);
        $this->shouldThrow(\InvalidArgumentException::class)->during('add', [$domainEvent3]);
    }
}
