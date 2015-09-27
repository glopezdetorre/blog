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

    /**
     * @var string
     */
    private $postSlug;

    /**
     * @param PostId $postId
     * @param string $postTitle
     * @param string $postSlug
     * @param string $postContent
     */
    public function __construct(PostId $postId, $postTitle, $postSlug, $postContent)
    {
        $this->postId = $postId;
        $this->postTitle = $postTitle;
        $this->postContent = $postContent;
        $this->postSlug = $postSlug;
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

    public function postSlug()
    {
        return $this->postSlug;
    }

    public function messageName()
    {
        return 'blog:post_was_created';
    }
}
