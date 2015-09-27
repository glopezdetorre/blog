<?php

namespace Gorka\Blog\Domain\Event\Post;

use Gorka\Blog\Domain\Event\DomainEvent;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Domain\Model\Post\Tag;

class PostWasUntagged implements DomainEvent
{
    /**
     * @var PostId
     */
    private $postId;

    /**
     * @var string
     */
    private $tagName;

    /**
     * @param PostId $postId
     * @param string $tagName
     */
    public function __construct(PostId $postId, $tagName)
    {
        $this->postId = $postId;
        $this->tagName = $tagName;
    }

    /**
     * @return PostId
     */
    public function postId()
    {
        return $this->postId;
    }

    /**
     * @return string
     */
    public function tagName()
    {
        return $this->tagName;
    }

    /**
     * @return PostId
     */
    public function aggregateId()
    {
        return $this->postId;
    }

    public function messageName()
    {
        return 'blog:post_was_untagged';
    }
}
