<?php

namespace spec\Gorka\Blog\Domain\Command\Post;

use Gorka\Blog\Domain\Command\Post\CreatePost;
use Gorka\Blog\Domain\Command\Post\CreatePostHandler;
use Gorka\Blog\Domain\Event\Post\PostWasCreated;
use Gorka\Blog\Domain\Model\AggregateHistory;
use Gorka\Blog\Domain\Model\EventStore;
use Gorka\Blog\Domain\Model\Post\PostId;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SimpleBus\Message\Bus\MessageBus;

class CreatePostHandlerSpec extends ObjectBehavior
{
    const POST_TITLE = 'Title';
    const POST_CONTENT = 'Post content';
    const POST_ID = 'a54a1776-d347-4e75-8e8a-b6ebf034b912';

    function let(EventStore $eventStore, MessageBus $eventBus)
    {
        $this->beConstructedWith($eventStore, $eventBus);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CreatePostHandler::class);
    }

    function it_should_commit_create_post_events(EventStore $eventStore, MessageBus $eventBus, CreatePost $command)
    {
        $id = PostId::create(self::POST_ID);
        $command->postId()->willReturn($id);
        $command->postTitle()->willReturn(self::POST_TITLE);
        $command->postContent()->willReturn(self::POST_CONTENT);
        $expectedEvents = [
            new PostWasCreated($id, self::POST_TITLE, self::POST_CONTENT)
        ];

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
