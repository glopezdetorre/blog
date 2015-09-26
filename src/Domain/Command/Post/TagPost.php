<?php

namespace Gorka\Blog\Domain\Command\Post;

use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Domain\Model\Post\Tag;

class TagPost
{
    /**
     * @var PostId
     */
    private $postId;

    /**
     * @var Tag
     */
    private $tag;

    public function __construct(PostId $postId, Tag $tag)
    {
        $this->postId = $postId;
        $this->tag = $tag;
    }

    public function postId()
    {
        return $this->postId;
    }

    public function tag()
    {
        return $this->tag;
    }

    public function aggregateId()
    {
        return $this->postId;
    }
}
