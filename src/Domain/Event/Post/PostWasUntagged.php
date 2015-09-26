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
     * @var Tag
     */
    private $tag;

    /**
     * @param PostId $postId
     * @param Tag $tag
     */
    public function __construct(PostId $postId, Tag $tag)
    {
        $this->postId = $postId;
        $this->tag = $tag;
    }

    /**
     * @return PostId
     */
    public function postId()
    {
        return $this->postId;
    }

    /**
     * @return Tag
     */
    public function tag()
    {
        return $this->tag;
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
