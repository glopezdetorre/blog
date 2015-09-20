<?php

namespace Gorka\Blog\Domain\Event\Post;

use Gorka\Blog\Domain\Event\DomainEvent;
use Gorka\Blog\Domain\Model\Post\PostId;

class PostWasCreated implements DomainEvent
{
    /**
     * @var PostId
     */
    private $postId;

    /**
     * @var string
     */
    private $postTitle;

    /**
     * @var string
     */
    private $postContent;

    public function __construct(PostId $postId, $postTitle, $postContent)
    {
        $this->postId = $postId;
        $this->postTitle = $postTitle;
        $this->postContent = $postContent;
    }

    public function aggregateId()
    {
        return $this->postId;
    }

    public function postId()
    {
        return $this->aggregateId();
    }

    public function postTitle()
    {
        return $this->postTitle;
    }

    public function postContent()
    {
        return $this->postContent;
    }

    public function messageName()
    {
        return 'blog:post_was_created';
    }
}
