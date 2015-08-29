<?php

namespace spec\Gorka\Blog\Domain\Model;

use Gorka\Blog\Domain\Event\DomainEvent;
use Gorka\Blog\Domain\Model\EventHistory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EventHistorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(EventHistory::class);
    }

    function it_should_be_initializable_empty()
    {
        $this->beConstructedWith([]);
        $this->events()->shouldBe([]);
    }

    function it_should_be_initializable_with_an_array_of_events(DomainEvent $event1, DomainEvent $event2)
    {
        $this->beConstructedWith([$event1, $event2]);
        $this->events()->shouldBe([$event1, $event2]);
    }

    function it_should_be_initializable_with_a_single_event(DomainEvent $event)
    {
        $this->beConstructedWith($event);
        $this->events()->shouldBe([$event]);
    }

    function it_should_allow_adding_new_events(DomainEvent $event)
    {
        $this->add($event);
        $this->events()->shouldBe([$event]);
    }

    function it_should_not_allow_non_domainevent_items_on_initialization(DomainEvent $event)
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('__construct', [[$event, new \StdClass()]]);
    }

    function it_should_allow_clearing_history(DomainEvent $event1, DomainEvent $event2, DomainEvent $event3) {
        $this->beConstructedWith([$event1, $event2]);
        $this->add($event3);
        $this->clear();
        $this->events()->shouldBe([]);
    }

    function it_should_implement_iterator_aggregate()
    {
        $this->shouldImplement(\IteratorAggregate::class);
        $this->getIterator()->shouldBeAnInstanceOf(\ArrayIterator::class);
    }
}
