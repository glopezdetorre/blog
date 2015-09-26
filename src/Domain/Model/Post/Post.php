<?php

namespace Gorka\Blog\Domain\Model\Post;

use Assert\Assertion;
use Doctrine\Common\Collections\ArrayCollection;
use Gorka\Blog\Domain\Event\DomainEvent;
use Gorka\Blog\Domain\Event\Post\PostContentWasChanged;
use Gorka\Blog\Domain\Event\Post\PostTitleWasChanged;
use Gorka\Blog\Domain\Event\Post\PostWasCreated;
use Gorka\Blog\Domain\Event\Post\PostWasPublished;
use Gorka\Blog\Domain\Event\Post\PostWasUnpublished;
use Gorka\Blog\Domain\Event\Post\PostWasTagged;
use Gorka\Blog\Domain\Event\Post\PostWasUntagged;
use Gorka\Blog\Domain\Model\AggregateHistory;
use Gorka\Blog\Domain\Model\EventRecorder;

class Post extends EventRecorder
{
    /**
     * @var PostId
     */
    private $id;

    /**
     * @var bool
     */
    private $published;

    /**
     * @var Tag[]
     */
    private $tags;

    /**
     * @param PostId $id
     */
    private function __construct(PostId $id)
    {
        $this->id = $id;
        $this->published = false;
        $this->tags = new ArrayCollection();
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

    public function addTag(Tag $tag)
    {
        if (in_array($tag, $this->tags->getValues())) {
            return;
        }

        $this->tags->add($tag);
        $this->recordThat(new PostWasTagged($this->id, $tag));
    }

    public function removeTag(Tag $tag)
    {
        if (!in_array($tag, $this->tags->getValues())) {
            return;
        }

        $this->tags->removeElement($tag);
        $this->recordThat(new PostWasUntagged($this->id, $tag));
    }

    public static function reconstituteFromEvents(AggregateHistory $aggregateHistory)
    {
        $id = PostId::create($aggregateHistory->aggregateId());
        $post = new Post($id);
        foreach ($aggregateHistory->events() as $event) {
            $post->apply($event);
        }
        return $post;
    }

    protected function apply(DomainEvent $event)
    {
        $methodName = 'apply'.(new \ReflectionClass($event))->getShortName();
        if (method_exists($this, $methodName)) {
            $this->$methodName($event);
        }
    }

    private function applyPostWasPublished(PostWasPublished $event)
    {
        $this->published = true;
    }

    private function applyPostWasUnpublished(PostWasUnpublished $event)
    {
        $this->published = false;
    }

    private function applyPostWasTagged(PostWasTagged $event)
    {
        $this->tags->add($event->tag());
    }

    private function applyPostWasUntagged(PostWasUntagged $event)
    {
        $this->tags->removeElement($event->tag());
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
