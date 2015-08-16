<?php

namespace spec\Gorka\Blog\Domain\Model;

use Gorka\Blog\Domain\Model\AggregateHistory;
use Gorka\Blog\Domain\Model\AggregateId;
use Gorka\Blog\Domain\Model\EventHistory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AggregateHistorySpec extends ObjectBehavior
{
    function let(AggregateId $id, EventHistory $events) {
        $this->beConstructedWith($id, $events);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AggregateHistory::class);
    }
}
