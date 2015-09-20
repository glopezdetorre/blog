<?php

namespace Gorka\Blog\Domain\Command\Post;

use Gorka\Blog\Domain\Command\DomainCommand;
use Gorka\Blog\Domain\Model\Post\PostId;

class PublishPost implements DomainCommand
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

    public function messageName()
    {
        return 'blog:publish_post';
    }
}
