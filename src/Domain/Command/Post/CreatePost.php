<?php

namespace Gorka\Blog\Domain\Command\Post;

use Gorka\Blog\Domain\Command\DomainCommand;
use Gorka\Blog\Domain\Model\Post\PostId;

class CreatePost implements DomainCommand
{

    /**
     * @var PostId
     */
    private $postId;

    /**
     * @var string
     */
    private $postTitle;

    /**
     * @var string
     */
    private $postContent;

    /**
     * @var
     */
    private $postSlug;

    public function __construct(PostId $id, $postTitle, $postSlug, $postContent)
    {
        $this->postId = $id;
        $this->postTitle = $postTitle;
        $this->postContent = $postContent;
        $this->postSlug = $postSlug;
    }

    public function postId()
    {
        return $this->postId;
    }

    public function postTitle()
    {
        return $this->postTitle;
    }

    public function postContent()
    {
        return $this->postContent;
    }

    public function postSlug()
    {
        return $this->postSlug;
    }

    public function messageName()
    {
        return 'blog:create_post';
    }
}
