<?php

namespace Gorka\Blog\Domain\Model\Post;

use Assert\Assertion;
use Gorka\Blog\Domain\Event\DomainEvent;
use Gorka\Blog\Domain\Event\Post\PostContentWasChanged;
use Gorka\Blog\Domain\Event\Post\PostTitleWasChanged;
use Gorka\Blog\Domain\Event\Post\PostWasCreated;
use Gorka\Blog\Domain\Event\Post\PostWasPublished;
use Gorka\Blog\Domain\Event\Post\PostWasUnpublished;
use Gorka\Blog\Domain\Model\AggregateHistory;
use Gorka\Blog\Domain\Model\EventHistory;
use Gorka\Blog\Domain\Model\EventRecording;

class Post implements EventRecording
{
    /** @var PostId */
    private $id;

    /** @var EventHistory */
    private $events;

    /** @var bool */
    private $published;

    /**
     * @param PostId $id
     */
    private function __construct(PostId $id)
    {
        $this->id = $id;
        $this->published = false;
        $this->events = new AggregateHistory($id);
    }

    public static function create(PostId $id, $title, $content)
    {
        $post = new static($id);
        $post->guardTitle($title);
        $post->guardContent($content);
        $post->recordThat(new PostWasCreated($id, $title, $content));
        return $post;
    }

    public function id()
    {
        return $this->id;
    }

    public function changeTitle($title)
    {
        $this->guardTitle($title);
        $this->recordThat(new PostTitleWasChanged($this->id, $title));
    }

    public function changeContent($content)
    {
        $this->guardContent($content);
        $this->recordThat(new PostContentWasChanged($this->id, $content));
    }

    public function publish()
    {
        if ($this->published) {
            return;
        }

        $this->recordThat(new PostWasPublished($this->id));
    }

    public function unpublish()
    {
        if (!$this->published) {
            return;
        }

        $this->recordThat(new PostWasUnpublished($this->id));
    }

    public function recordedEvents()
    {
        return $this->events;
    }

    public function recordThat(DomainEvent $event)
    {
        $this->events->add($event);
        $this->apply($event);
    }

    public static function reconstituteFromEvents(AggregateHistory $aggregateHistory)
    {
        $id = $aggregateHistory->aggregateId();
        $postId = PostId::create($id->id());
        $post = new Post($postId);

        foreach ($aggregateHistory->events() as $event) {
            $post->apply($event);
        }
        return $post;
    }

    private function apply(DomainEvent $event)
    {
        $methodName = 'apply'.(new \ReflectionClass($event))->getShortName();
        if (method_exists($this, $methodName)) {
            $this->$methodName($event);
        }
    }

    private function applyPostWasPublished(DomainEvent $event)
    {
        $this->published = true;
    }

    private function applyPostWasUnpublished(DomainEvent $event)
    {
        $this->published = false;
    }

    /**
     * @param $title
     */
    private function guardTitle($title)
    {
        Assertion::string($title, 'Title should be a string');
        Assertion::notBlank(trim($title), 'Title cannot be blank');
    }

    /**
     * @param $content
     */
    private function guardContent($content)
    {
        Assertion::string($content, 'Content should be a string');
        Assertion::notBlank(trim($content), 'Content cannot be blank');
    }
}
