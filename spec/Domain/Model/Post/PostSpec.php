<?php

namespace spec\Gorka\Blog\Domain\Model\Post;

use Gorka\Blog\Domain\Event\DomainEvent;
use Gorka\Blog\Domain\Event\Post\PostContentWasChanged;
use Gorka\Blog\Domain\Event\Post\PostTitleWasChanged;
use Gorka\Blog\Domain\Event\Post\PostWasPublished;
use Gorka\Blog\Domain\Event\Post\PostWasUnpublished;
use Gorka\Blog\Domain\Model\AggregateHistory;
use Gorka\Blog\Domain\Model\Post\Post;
use Gorka\Blog\Domain\Model\Post\PostId;
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PostSpec extends ObjectBehavior
{
    const TEST_CONTENT = 'My content';
    const TEST_TITLE = 'My title';
    const POST_ID = '25769c6c-d34d-4bfe-ba98-e0ee856f3e7a';

    function let()
    {
        $this->beConstructedThrough(
            'create', [
                PostId::create(self::POST_ID),
                self::TEST_TITLE,
                self::TEST_CONTENT
            ]
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Post::class);
    }

    function it_should_have_an_id()
    {
        $this->id()->shouldBeLike(PostId::create(self::POST_ID));
    }

    function it_should_record_title_changes()
    {
        $newTitle = 'New title';
        $expectedEvent = new PostTitleWasChanged(PostId::create(self::POST_ID), $newTitle);

        $this->changeTitle($newTitle);
        $this->recordedEvents()->shouldContainEvent($expectedEvent);
    }

    function it_should_not_allow_non_string_or_empty_titles()
    {
        $badTitles = [
            null,
            new \StdClass(),
            true,
            123,
            '',
            '   ',
            "\t",
            "\n",
            "\t  \n"
        ];

        foreach ($badTitles as $badTitle) {
            $this
                ->shouldThrow(\InvalidArgumentException::class)
                ->during('create', [PostId::create(self::POST_ID), $badTitle, self::TEST_CONTENT]);
        }

        foreach ($badTitles as $badTitle) {
            $this
                ->shouldThrow(\InvalidArgumentException::class)
                ->during('changeTitle', [$badTitle]);
        }
    }

    function it_should_record_content_changes()
    {
        $newContent = 'New content';
        $expectedEvent = new PostContentWasChanged(PostId::create(self::POST_ID), $newContent);

        $this->changeContent($newContent);
        $this->recordedEvents()->shouldContainEvent($expectedEvent);
    }

    function it_should_not_allow_non_string_or_empty_content()
    {
        $badContents = [
            null,
            new \StdClass(),
            true,
            123,
            '',
            '   ',
            "\t",
            "\n",
            "\t  \n"
        ];

        foreach ($badContents as $badContent) {
            $this
                ->shouldThrow(\InvalidArgumentException::class)
                ->during('create', [PostId::create(self::POST_ID), self::TEST_TITLE, $badContent]);
        }

        foreach ($badContents as $badContent) {
            $this->shouldThrow(\InvalidArgumentException::class)->during('changeContent', [$badContent]);
        }
    }

    function it_should_record_publishing_on_unpublished_posts()
    {
        $expectedEvent = new PostWasPublished(PostId::create(self::POST_ID));
        $this->publish();
        $this->recordedEvents()->shouldContainEventTimes($expectedEvent, 1);
    }

    function it_should_record_unpublishing_on_published_posts()
    {
        $expectedEvent = new PostWasUnpublished(PostId::create(self::POST_ID));
        $this->publish();
        $this->unpublish();
        $this->recordedEvents()->shouldContainEvent($expectedEvent);
    }

    function it_should_not_record_publishing_on_already_published_posts()
    {
        $expectedEvent = new PostWasPublished(PostId::create(self::POST_ID));
        $this->publish();
        $this->publish();
        $this->recordedEvents()->shouldContainEventTimes($expectedEvent, 1);
    }

    function it_should_not_record_unpublishing_on_already_unpublished_posts()
    {
        $unexpectedEvent = new PostWasUnpublished(PostId::create(self::POST_ID));
        $this->unpublish();
        $this->recordedEvents()->shouldNotContainEvent($unexpectedEvent);
    }

    function getMatchers() {
        return [
            'containEvent' => function (AggregateHistory $subject, DomainEvent $event) {
                $events = array_filter($subject->events(), function ($a) use ($event) { return $a == $event; });
                return (count($events) > 0);
            },
            'containEventTimes' => function (AggregateHistory $subject, DomainEvent $event, $times) {
                $events = array_filter($subject->events(), function ($a) use ($event) { return $a == $event; });
                if (count($events) !== $times) {
                    throw new FailureException(
                        sprintf(
                            "AggregateHistory was expected to contain '%s' event %d times, but %d were found",
                            (new \ReflectionClass($event))->getShortName(),
                            $times,
                            count($events)
                        )
                    );
                } else {
                    return true;
                }
            }
        ];
    }
}
