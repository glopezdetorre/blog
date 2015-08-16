<?php

namespace spec\Gorka\Blog\Infrastructure\Data\Post;

use Gorka\Blog\Domain\Model\AggregateHistory;
use Gorka\Blog\Domain\Model\AggregateId;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Infrastructure\Data\EventStore;
use Gorka\Blog\Infrastructure\Data\Post\PostRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PostRepositorySpec extends ObjectBehavior
{
    function let(EventStore $eventStore)
    {
        $this->beConstructedWith($eventStore);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PostRepository::class);
    }

    function it_should_delegate_commits_to_event_store(EventStore $eventStore, AggregateHistory $aggregateHistory)
    {
        $eventStore->commit($aggregateHistory)->shouldBeCalled();
        $this->commit($aggregateHistory);
    }

    function it_should_delegate_aggregate_history_retrieval_to_event_store(
        EventStore $eventStore,
        PostId $postId,
        AggregateHistory $aggregateHistory
    ) {
        $eventStore->aggregateHistory($postId)->willReturn($aggregateHistory);
        $eventStore->aggregateHistory($postId)->shouldBeCalled();
        $this->history($postId)->shouldReturn($aggregateHistory);
    }
}
