<?php

namespace Gorka\Blog\Domain\Event\Post;

use Gorka\Blog\Domain\Event\DomainEvent;
use Gorka\Blog\Domain\Model\AggregateId;
use Gorka\Blog\Domain\Model\Post\PostId;

class PostContentWasChanged implements DomainEvent
{

    /**
     * @var PostId
     */
    private $id;

    /**
     * @var string
     */
    private $postContent;

    public function __construct(PostId $id, $postContent)
    {
        $this->id = $id;
        $this->postContent = $postContent;
    }

    public function postContent()
    {
        return $this->postContent;
    }

    /**
     * @return AggregateId
     */
    public function aggregateId()
    {
        return $this->id;
    }
}
