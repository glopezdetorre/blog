<?php

namespace Gorka\Blog\Domain\Event\Post;

use Gorka\Blog\Domain\Event\DomainEvent;
use Gorka\Blog\Domain\Model\AggregateId;
use Gorka\Blog\Domain\Model\Post\PostId;

class PostSlugWasChanged implements DomainEvent
{

    /**
     * @var PostId
     */
    private $id;

    /**
     * @var string
     */
    private $postSlug;

    public function __construct(PostId $id, $postSlug)
    {
        $this->id = $id;
        $this->postSlug = $postSlug;
    }

    public function postSlug()
    {
        return $this->postSlug;
    }

    /**
     * @return AggregateId
     */
    public function aggregateId()
    {
        return $this->id;
    }
}