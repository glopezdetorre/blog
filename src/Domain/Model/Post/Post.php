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
        $this->tags = [];
    }

    public static function create(PostId $id, $title, $slug, $content)
    {
        $post = new static($id);
        $post->guardTitle($title);
        $post->guardSlug($slug);
        $post->guardContent($content);
        $post->recordThat(new PostWasCreated($id, $title, $slug, $content));
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

    /**
     * @param Tag $tag
     */
    public function addTag(Tag $tag)
    {
        if (in_array($tag->name(), $this->tags)) {
            return;
        }

        $this->tags[] = $tag->name();
        $this->recordThat(new PostWasTagged($this->id, $tag));
    }

    /**
     * @param string $tagName
     */
    public function removeTag($tagName)
    {
        Assertion::string($tagName);
        $key = array_search($tagName, $this->tags);
        if (false === $key) {
            return;
        }

        unset($this->tags[$key]);
        $this->recordThat(new PostWasUntagged($this->id, $tagName));
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
        $this->tags[] = $event->tag()->name();
    }

    private function applyPostWasUntagged(PostWasUntagged $event)
    {
        $key = array_search($event->tagName(), $this->tags, true);
        if (false !== $key) {
            unset($this->tags[$key]);
        }
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

    private function guardSlug($slug)
    {
        Assertion::string($slug, 'Slug should be a string');
        Assertion::notBlank(trim($slug), 'Slug cannot be blank');
        Assertion::false(preg_replace('/[a-z0-9]/i', '', $slug) == $slug, 'Slug should have content');
    }
}
