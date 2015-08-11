<?php

namespace Gorka\Blog\Domain\Event\Post;

use Gorka\Blog\Domain\Event\DomainEvent;
use Gorka\Blog\Domain\Model\Post\PostId;

class PostWasRead implements DomainEvent
{

    /**
     * @var PostId
     */
    private $postId;

    public function __construct(PostId $postId)
    {
        $this->postId = $postId;
    }

    public function aggregateId()
    {
        return $this->postId;
    }
}
