<?php

namespace Gorka\Blog\Domain\Command\Post;

use Gorka\Blog\Domain\Command\DomainCommand;
use Gorka\Blog\Domain\Model\Post\PostId;

class TagPost implements DomainCommand
{
    /**
     * @var PostId
     */
    private $postId;

    /**
     * @var string
     */
    private $tagName;

    public function __construct(PostId $postId, $tagName)
    {
        $this->postId = $postId;
        $this->tagName = $tagName;
    }

    public function postId()
    {
        return $this->postId;
    }

    public function tagName()
    {
        return $this->tagName;
    }

    public function aggregateId()
    {
        return $this->postId;
    }

    public function messageName()
    {
        return 'blog:tag_post';
    }
}
