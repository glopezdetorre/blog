<?php

namespace Gorka\Blog\Domain\Command\Post;

class CreatePost
{

    /** @var  string */
    private $postTitle;

    /**
     * @var \DateTimeImmutable
     */
    private $postCreationDateTime;

    /** @var  string */
    private $postContent;

    public function __construct($postTitle, \DateTimeImmutable $postCreationDateTime, $postContent)
    {
        $this->postTitle = $postTitle;
        $this->postCreationDateTime = $postCreationDateTime;
        $this->postContent = $postContent;
    }

    public function postTitle()
    {
        return $this->postTitle;
    }

    public function postCreationDateTime()
    {
        return $this->postCreationDateTime;
    }

    public function postContent()
    {
        return $this->postContent;
    }
}
