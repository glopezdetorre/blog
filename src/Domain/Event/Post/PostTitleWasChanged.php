<?php

namespace Gorka\Blog\Domain\Event\Post;

use Gorka\Blog\Domain\Event\DomainEvent;
use Gorka\Blog\Domain\Model\AggregateId;
use Gorka\Blog\Domain\Model\Post\PostId;

class PostTitleWasChanged implements DomainEvent
{
    /**
     * @var PostId
     */
    private $id;

    /**
     * @var string
     */
    private $postTitle;

    /**
     * @param PostId $id
     */
    public function __construct(PostId $id, $postTitle)
    {
        $this->id = $id;
        $this->postTitle = $postTitle;
    }

    public function postId()
    {
        return $this->aggregateId();
    }

    /**
     * @return string
     */
    public function postTitle()
    {
        return $this->postTitle;
    }

    /**
     * @return AggregateId
     */
    public function aggregateId()
    {
        return $this->id;
    }

    public function messageName()
    {
        return 'blog:post_title_was_changed';
    }
}
