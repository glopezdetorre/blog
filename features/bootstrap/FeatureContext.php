<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Gorka\Blog\Domain\Event\DomainEvent;
use Gorka\Blog\Domain\Event\Post\PostContentWasChanged;
use Gorka\Blog\Domain\Event\Post\PostTitleWasChanged;
use Gorka\Blog\Domain\Event\Post\PostWasCreated;
use Gorka\Blog\Domain\Event\Post\PostWasPublished;
use Gorka\Blog\Domain\Event\Post\PostWasUnpublished;
use Gorka\Blog\Domain\Model\AggregateHistory;
use Gorka\Blog\Domain\Model\EventHistory;
use Gorka\Blog\Domain\Model\Post\Post;
use Gorka\Blog\Domain\Model\Post\PostId;

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
    private $eventsInGivenStep;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @Given No post exists with id :id
     */
    public function NoPostExistsWithId($id) {

    }

    /**
     * @When I call create post with id :id, title :title and content :content
     */
    public function ICallCreatePostWithIdTitleAndContent($id, $title, $content)
    {
        try {
            $this->post = Post::create(PostId::create($id), $title, $content);
        } catch (\Exception $e) {
            $this->exception = $e;
        }
    }

    /**
     * @Then I should see a PostWasCreated event with id :id, title :title and content :content
     */
    public function iShouldSeeAPostwascreatedEventWithIdTitleAndContent($id, $title, $content)
    {
        $events = $this->post->recordedEvents()->events();
        if (count($events) !== 1) {
            throw new Exception("Expected 1 event, but I saw ".count($events).".");
        }

        $event = $events[0];
        if (!($event instanceof PostWasCreated)) {
            throw new Exception("Expected PostWasCreated event, but I saw ".get_class($event).".");
        }

        if ($event->aggregateId() != $id) {
            throw new Exception("Expected id '$id', but I saw '{$event->aggregateId()}'");
        }

        if ($event->postTitle() != $title) {
            throw new Exception("Expected title '$title', but I saw '{$event->postTitle()}'");
        }

        if ($event->postContent() != $content) {
            throw new Exception("Expected content '$content', but I saw '{$event->postContent()}'");
        }
    }

    /**
     * @Given A post with id :id exists with title :title and content :content
     */
    public function APostWithIdExistsWithTitleAndContent($id, $title, $content)
    {
        $history = new EventHistory();
        $history->add(new PostWasCreated(PostId::create($id), $title, $content));

        $this->post = Post::reconstituteFromEvents(
            new AggregateHistory(
                PostId::create($id),
                $history
            )
        );

        $this->eventsInGivenStep = $this->post->recordedEvents();
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
     * @Then I should see a PostTitleWasChanged event with id :id and title :title
     */
    public function IShouldSeeAPosttitlewaschangedEventWithIdAndTitle($id, $title)
    {
        $events = $this->post->recordedEvents()->events();
        foreach ($events as $event) {
            if (!($event instanceof PostTitleWasChanged)) {
                continue;
            } elseif ($event->aggregateId() != $id || $event->postTitle() !== $title) {
                continue;
            } else {
                return;
            }
        }
        throw new \Exception("Expected PostTitleWasChanged event with id '{$id}' and title '{$title}' was not found");
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
        $events = $this->post->recordedEvents()->events();
        foreach ($events as $event) {
            if (!($event instanceof PostContentWasChanged)) {
                continue;
            } elseif ($event->aggregateId() != $id || $event->postContent() !== $content) {
                continue;
            } else {
                return;
            }
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
        $events = [];
        if ($this->post !== null) {
            $events = $this->post->recordedEvents();
        }

        if ((count($events) - count($this->eventsInGivenStep)) > 0) {
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
        $events = $this->post->recordedEvents()->events();
        foreach ($events as $event) {
            if (!($event instanceof PostWasPublished)) {
                continue;
            } elseif ($event->aggregateId() != $id) {
                continue;
            } else {
                return;
            }
        }
        throw new \Exception("Expected PostWasPublished event with id '{$id}' was not found");
    }

    /**
     * @Given Post is published
     */
    public function postIsPublished()
    {
        $this->post->publish();
        $this->eventsInGivenStep = $this->post->recordedEvents();
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
        $events = $this->post->recordedEvents()->events();
        foreach ($events as $event) {
            if (!($event instanceof PostWasUnpublished)) {
                continue;
            } elseif ($event->aggregateId() != $id) {
                continue;
            } else {
                return;
            }
        }
        throw new \Exception("Expected PostWasUnpublished event with id '{$id}' was not found");
    }
}
