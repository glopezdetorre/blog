<?php

namespace spec\Gorka\Blog\Domain\Command\Post;

use Gorka\Blog\Domain\Command\Post\TagPost;
use Gorka\Blog\Domain\Command\Post\TagPostHandler;
use Gorka\Blog\Domain\Event\Post\PostWasCreated;
use Gorka\Blog\Domain\Event\Post\PostWasTagged;
use Gorka\Blog\Domain\Model\AggregateHistory;
use Gorka\Blog\Domain\Model\EventStore;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Domain\Model\Post\Tag;
use Gorka\Blog\Domain\Service\Slugifier;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SimpleBus\Message\Bus\MessageBus;

class TagPostHandlerSpec extends ObjectBehavior
{
    const POST_ID = 'a54a1776-d347-4e75-8e8a-b6ebf034b912';
    const POST_TITLE = 'My Title';
    const POST_SLUG = 'my-title';
    const POST_CONTENT = 'Content';
    const TEST_TAG_NAME = 'My tag';
    const TEST_TAG_SLUG = 'my-tag';

    function let(EventStore $eventStore, MessageBus $eventBus, Slugifier $slugifier)
    {
        $slugifier->slugify(self::TEST_TAG_NAME)->willReturn(self::TEST_TAG_SLUG);
        $this->beConstructedWith($eventStore, $eventBus, $slugifier);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TagPostHandler::class);
    }

    function it_should_commit_tag_post_events(EventStore $eventStore, MessageBus $eventBus, TagPost $command)
    {
        $id = PostId::create(self::POST_ID);
        $tag = Tag::create(self::TEST_TAG_NAME, self::TEST_TAG_SLUG);
        $command->postId()->willReturn($id);
        $command->tagName()->willReturn(self::TEST_TAG_NAME);
        $expectedEvents = [
            new PostWasTagged($id, $tag)
        ];

        $eventStore->events($id)->willReturn(
            [
                new PostWasCreated($id, self::POST_TITLE, self::POST_SLUG, self::POST_CONTENT)
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
