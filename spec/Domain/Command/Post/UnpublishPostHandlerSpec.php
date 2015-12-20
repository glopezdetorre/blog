<?php

namespace spec\Gorka\Blog\Domain\Command\Post;

use Gorka\Blog\Domain\Command\Post\UnpublishPost;
use Gorka\Blog\Domain\Command\Post\UnpublishPostHandler;
use Gorka\Blog\Domain\Event\Post\PostWasCreated;
use Gorka\Blog\Domain\Event\Post\PostWasPublished;
use Gorka\Blog\Domain\Event\Post\PostWasUnpublished;
use Gorka\Blog\Domain\Model\AggregateHistory;
use Gorka\Blog\Domain\Model\EventStore;
use Gorka\Blog\Domain\Model\Post\PostId;
use PhpSpec\ObjectBehavior;
use Prooph\ServiceBus\EventBus;
use Prophecy\Argument;

/** @mixin UnpublishPostHandler */
class UnpublishPostHandlerSpec extends ObjectBehavior
{
    const POST_ID = 'a54a1776-d347-4e75-8e8a-b6ebf034b912';
    const POST_TITLE = 'My Title';
    const POST_SLUG = 'my-title';
    const POST_CONTENT = 'Content';

    function let(EventStore $eventStore, EventBus $eventBus)
    {
        $this->beConstructedWith($eventStore, $eventBus);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UnpublishPostHandler::class);
    }

    function it_should_commit_unpublish_post_events(EventStore $eventStore, EventBus $eventBus, UnpublishPost $command)
    {
        $id = PostId::create(self::POST_ID);
        $command->postId()->willReturn($id);
        $expectedEvents = [
            new PostWasUnpublished($id)
        ];

        $eventStore->events($id)->willReturn(
            [
                new PostWasCreated($id, self::POST_TITLE, self::POST_SLUG, self::POST_CONTENT),
                new PostWasPublished($id)
            ]
        );

        $eventStore->commit(
            new AggregateHistory(
                $id,
                $expectedEvents
            )
        )->shouldBeCalled();

        foreach ($expectedEvents as $expectedEvent) {
            $eventBus->dispatch($expectedEvent)->shouldBeCalled();
        }

        $this->handle($command);
    }
}
