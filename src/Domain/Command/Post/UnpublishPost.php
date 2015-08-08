<?php

namespace Gorka\Blog\Domain\Command\Post;

use Gorka\Blog\Domain\Model\Post\PostId;

class UnpublishPost
{
    /**
     * @var PostId
     */
    private $postId;

    /**
     * @param PostId $postId
     */
    public function __construct(PostId $postId)
    {

        $this->postId = $postId;
    }

    public function postId()
    {
        return $this->postId;
    }
}
