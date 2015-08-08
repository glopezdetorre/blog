<?php

namespace Gorka\Blog\Domain\Event\Post;

use Gorka\Blog\Domain\Model\Post\PostId;

class PostWasCreated
{

    /**
     * @var PostId
     */
    private $postId;

    public function __construct(PostId $postId)
    {
        $this->postId = $postId;
    }

    public function postId()
    {
        return $this->postId;
    }
}
