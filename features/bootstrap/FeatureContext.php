<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Gorka\Blog\Domain\Event\DomainEvent;
use Gorka\Blog\Domain\Event\Post\PostContentWasChanged;
use Gorka\Blog\Domain\Event\Post\PostTitleWasChanged;
use Gorka\Blog\Domain\Event\Post\PostWasCreated;
use Gorka\Blog\Domain\Event\Post\PostWasPublished;
use Gorka\Blog\Domain\Event\Post\PostWasTagged;
use Gorka\Blog\Domain\Event\Post\PostWasUnpublished;
use Gorka\Blog\Domain\Event\Post\PostWasUntagged;
use Gorka\Blog\Domain\Model\AggregateHistory;
use Gorka\Blog\Domain\Model\Post\Post;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Domain\Model\Post\Tag;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    /**
     * @var Post
     */
    private $post;

    /**
     * @var \Exception
     */
    private $exception;

    /**
     * @var DomainEvent[]
     */
    private $events;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->events = [];
    }


    /**
     * @Transform /^(.*)$/i
     */
    public function transformEscapedString($arg)
    {
        return str_replace(['\n', '\t'], ["\n", "\t"], $arg);
    }

    /**
     * @Transform /^null$/i
     */
    public function transformNull()
    {
        return null;
    }

    /**
     * @param DomainEvent[] $events
     * @param string $eventType
     * @return DomainEvent[]
     */
    private function filterByEventType($events, $eventType) {
        $filteredEvents = array_filter(
            $events,
            function($event) use ($eventType) {
                return ($event instanceof $eventType);
            }
        );
        return $filteredEvents;
    }

    /**
     * @Given No post exists with id :id
     */
    public function NoPostExistsWithId($id)
    {

    }

    /**
     * @When I call create post with id :id, title :title, slug :slug and content :content
     */
    public function ICallCreatePostWithIdTitleSlugAndContent($id, $title, $slug, $content)
    {
        try {
            $this->post = Post::create(PostId::create($id), $title, $slug, $content);
        } catch (\Exception $e) {
            $this->exception = $e;
        }
    }

    /**
     * @Then I should see a PostWasCreated event with id :id, title :title and content :content
     */
    public function iShouldSeeAPostwascreatedEventWithIdTitleAndContent($id, $title, $content)
    {
        $events = $this->filterByEventType($this->post?$this->post->recordedEvents():[], PostWasCreated::class);
        foreach ($events as $event) {
            if ($event->aggregateId() == $id && $event->postTitle() == $title && $event->postContent() == $content) {
                return;
            }
        }
        throw new Exception(
            "Expected PostWasCreated event with id '$id', title '$title' and content '$content' not found'"
        );
    }

    /**
     * @Given A post with id :id exists with title :title and content :content
     */
    public function APostWithIdExistsWithTitleAndContent($id, $title, $content)
    {
        $postId = PostId::create($id);
        $this->events[] = new PostWasCreated($postId, $title, null, $content);
        $this->post = Post::reconstituteFromEvents(new AggregateHistory($postId, $this->events));
    }

    /**
     * @When I call changeTitle with :newTitle
     */
    public function ICallChangeTitleWith($newTitle)
    {
        try {
            $this->post->changeTitle($newTitle);
        } catch (\Exception $e) {
            $this->exception = $e;
        }
    }

    /**
     * @Then I should see a PostTitleWasChanged event with id :id and title :newTitle
     */
    public function IShouldSeeAPosttitlewaschangedEventWithIdAndTitle($id, $newTitle)
    {
        $events = $this->filterByEventType($this->post->recordedEvents(), PostTitleWasChanged::class);
        foreach ($events as $event) {
            if ($event->aggregateId() == $id && $event->postTitle() == $newTitle) {
                return;
            }
        }
        throw new \Exception("Expected PostTitleWasChanged event with id '{$id}' and title '{$newTitle}' was not found");
    }

    /**
     * @When I call changeContent with :newContent
     */
    public function iCallChangecontentWith($newContent)
    {
        try {
            $this->post->changeContent($newContent);
        } catch (\Exception $e) {
            $this->exception = $e;
        }
    }

    /**
     * @Then I should see a PostContentWasChanged event with id :id and content :content
     */
    public function iShouldSeeAPostcontentwaschangedEventWithIdAndContent($id, $content)
    {
        $events = $this->filterByEventType($this->post->recordedEvents(), PostContentWasChanged::class);
        foreach ($events as $event) {
            if ($event->aggregateId() == $id) return;
        }
        throw new \Exception("Expected PostContentWasChanged event with id '{$id}' and title '{$content}' was not found");
    }

    /**
     * @Then I should see an Exception
     */
    public function iShouldSeeAnException()
    {
        if ($this->exception === null) {
            throw new \Exception("Expected exception not thrown");
        }
    }

    /**
     * @Then No new events should have been recorded
     */
    public function noNewEventsShouldHaveBeenRecorded()
    {
        $events = $this->post?$this->post->recordedEvents():[];
        if (count($events) > 0) {
            throw new \Exception("No events should have been recorded, ".count($events)." found");
        }
    }

    /**
     * @When I call publish
     */
    public function iCallPublish()
    {
        try {
            $this->post->publish();
        } catch (\Exception $e) {
            $this->exception = $e;
        }
    }

    /**
     * @Then I should see a PostWasPublished event with id :id
     */
    public function iShouldSeeAPostwaspublishedEventWithId($id)
    {
        $events = $this->filterByEventType($this->post?$this->post->recordedEvents():[], PostWasPublished::class);
        foreach ($events as $event) {
            if ($event->aggregateId() == $id) {
                return;
            }
        }
        throw new \Exception("Expected PostWasPublished event with id '{$id}' was not found");
    }

    /**
     * @When I call unpublish
     */
    public function iCallUnpublish()
    {
        try {
            $this->post->unpublish();
        } catch (\Exception $e) {
            $this->exception = $e;
        }
    }

    /**
     * @Then I should see a PostWasUnpublished event with id :id
     */
    public function iShouldSeeAPostwasunpublishedEventWithId($id)
    {
        $events = $this->filterByEventType($this->post?$this->post->recordedEvents():[], PostWasUnpublished::class);
        foreach ($events as $event) {
            if ($event->aggregateId() == $id) return;
        }
        throw new \Exception("Expected PostWasUnpublished event with id '{$id}' was not found");
    }

    /**
     * @Given Post with id :id is published
     */
    public function postWithIdIsPublished($id)
    {
        $postId = PostId::create($id);
        $this->events[] = new PostWasPublished($postId);
        $this->post = Post::reconstituteFromEvents(new AggregateHistory($postId, $this->events));
    }

    /**
     * @Given Post with id :id is unpublished
     */
    public function postWithIdIsUnpublished($id)
    {
        $postId = PostId::create($id);
        $this->events[] = new PostWasUnpublished($postId);
        $this->post = Post::reconstituteFromEvents(new AggregateHistory($postId, $this->events));
    }

    /**
     * @Given Post is not tagged with :tag
     */
    public function postIsNotTaggedWith($tag)
    {
        $this->events[] = new \Gorka\Blog\Domain\Event\Post\PostWasUntagged($this->post->id(), Tag::create($tag));
        $this->post = Post::reconstituteFromEvents(new AggregateHistory($this->post->id(), $this->events));
    }

    /**
     * @When I call addTag with :tag
     */
    public function iCallAddtagWith($tag)
    {
        $this->post->addTag(Tag::create($tag));
    }

    /**
     * @Then I should see a PostWasTagged event with id :id and tag :tag
     */
    public function iShouldSeeAPostwastaggedEventWithIdAndTag($id, $tag)
    {
        $tag = Tag::create($tag);
        $events = $this->filterByEventType($this->post?$this->post->recordedEvents():[], PostWasTagged::class);
        foreach ($events as $event) {
            if ($event->aggregateId() == $id && $event->tag() == $tag) return;
        }
        throw new \Exception("Expected PostWasTagged event with id '{$id}' and tag '{$tag}' was not found");
    }

    /**
     * @Given Post is tagged with :tag
     */
    public function postIsTaggedWith($tag)
    {
        $this->events[] = new PostWasTagged($this->post->id(), Tag::create($tag));
        $this->post = Post::reconstituteFromEvents(new AggregateHistory($this->post->id(), $this->events));
    }

    /**
     * @Then I should not see a PostWasTagged event with id :id and tag :tag
     */
    public function iShouldNotSeeAPostwastaggedEventWithIdAndTag($id, $tag)
    {
        try {
            $this->iShouldSeeAPostwastaggedEventWithIdAndTag($id, $tag);
            throw new \Exception("Unexpected PostWasTagged event with id '{$id}' and tag '{$tag}'");
        } catch (\Exception $e) {
            return;
        }
    }

    /**
     * @When I call removeTag with :tag
     */
    public function iCallRemovetagWith($tag)
    {
        $this->post->removeTag(Tag::create($tag));
    }

    /**
     * @Then I should see a PostWasUntagged event with id :id and tag :tag
     */
    public function iShouldSeeAPostwasuntaggedEventWithIdAndTag($id, $tag)
    {
        $tag = Tag::create($tag);
        $events = $this->filterByEventType($this->post?$this->post->recordedEvents():[], PostWasUntagged::class);
        foreach ($events as $event) {
            if ($event->aggregateId() == $id && $event->tag() == $tag) return;
        }
        throw new \Exception("Expected PostWasUntagged event with id '{$id}' and tag '{$tag}' was not found");
    }

    /**
     * @Then I should not see a PostWasUntagged event with id :id and tag :tag
     */
    public function iShouldNotSeeAPostwasuntaggedEventWithIdAndTag($id, $tag)
    {
        try {
            $this->iShouldSeeAPostwasuntaggedEventWithIdAndTag($id, $tag);
            throw new \Exception("Unexpected PostWasUntagged event with id '{$id}' and tag '{$tag}'");
        } catch (\Exception $e) {
            return;
        }
    }
}
