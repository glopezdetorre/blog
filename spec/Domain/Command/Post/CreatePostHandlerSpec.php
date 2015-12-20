<?php

namespace spec\Gorka\Blog\Domain\Command\Post;

use Gorka\Blog\Domain\Command\Post\CreatePost;
use Gorka\Blog\Domain\Command\Post\CreatePostHandler;
use Gorka\Blog\Domain\Event\Post\PostWasCreated;
use Gorka\Blog\Domain\Model\AggregateHistory;
use Gorka\Blog\Domain\Model\EventStore;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Domain\Service\Slugifier;
use PhpSpec\ObjectBehavior;
use Prooph\ServiceBus\EventBus;
use Prophecy\Argument;

/** @mixin CreatePostHandler */
class CreatePostHandlerSpec extends ObjectBehavior
{
    const POST_TITLE = 'My Title';
    const POST_SLUG = 'my-title';
    const POST_CONTENT = 'Post content';
    const POST_ID = 'a54a1776-d347-4e75-8e8a-b6ebf034b912';

    function let(EventStore $eventStore, EventBus $eventBus, Slugifier $slugifier)
    {
        $this->beConstructedWith($eventStore, $eventBus, $slugifier);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CreatePostHandler::class);
    }

    function it_should_commit_create_post_events(EventStore $eventStore, EventBus $eventBus, CreatePost $command)
    {
        $id = PostId::create(self::POST_ID);
        $command->postId()->willReturn($id);
        $command->postTitle()->willReturn(self::POST_TITLE);
        $command->postSlug()->willReturn(self::POST_SLUG);
        $command->postContent()->willReturn(self::POST_CONTENT);
        $expectedEvents = [
            new PostWasCreated($id, self::POST_TITLE, self::POST_SLUG, self::POST_CONTENT)
        ];

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

    function it_should_generate_slugs_if_empty(
        EventStore $eventStore,
        EventBus $eventBus,
        CreatePost $command,
        Slugifier $slugifier
    ) {
        $id = PostId::create(self::POST_ID);
        $command->postId()->willReturn($id);
        $command->postTitle()->willReturn(self::POST_TITLE);
        $command->postSlug()->willReturn(null);
        $command->postContent()->willReturn(self::POST_CONTENT);
        $slugifier->slugify(self::POST_TITLE)->willReturn(self::POST_SLUG);

        $expectedEvents = [
            new PostWasCreated($id, self::POST_TITLE, self::POST_SLUG, self::POST_CONTENT)
        ];

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
