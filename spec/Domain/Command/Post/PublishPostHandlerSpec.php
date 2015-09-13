<?php

namespace spec\Gorka\Blog\Domain\Command\Post;

use Gorka\Blog\Domain\Command\Post\PublishPost;
use Gorka\Blog\Domain\Command\Post\PublishPostHandler;
use Gorka\Blog\Domain\Event\Post\PostWasCreated;
use Gorka\Blog\Domain\Event\Post\PostWasPublished;
use Gorka\Blog\Domain\Model\AggregateHistory;
use Gorka\Blog\Domain\Model\EventStore;
use Gorka\Blog\Domain\Model\Post\PostId;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SimpleBus\Message\Bus\MessageBus;

class PublishPostHandlerSpec extends ObjectBehavior
{
    const POST_ID = 'a54a1776-d347-4e75-8e8a-b6ebf034b912';

    function let(EventStore $eventStore, MessageBus $eventBus)
    {
        $this->beConstructedWith($eventStore, $eventBus);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PublishPostHandler::class);
    }

    function it_should_commit_publish_post_events(EventStore $eventStore, MessageBus $eventBus, PublishPost $command)
    {
        $id = PostId::create(self::POST_ID);
        $command->postId()->willReturn($id);
        $expectedEvents = [
            new PostWasPublished($id)
        ];

        $eventStore->events($id)->willReturn(
            [
                new PostWasCreated($id, 'Title', 'Content')
            ]
        );

        $eventStore->commit(
            new AggregateHistory(
                $id,
                $expectedEvents
            )
        )->shouldBeCalled();

        foreach ($expectedEvents as $expectedEvent) {
            $eventBus->handle($expectedEvent)->shouldBeCalled();
        }

        $this->handle($command);
    }
}
