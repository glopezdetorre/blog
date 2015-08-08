<?php

namespace Gorka\Blog\Domain\Model\Post;

use Assert\Assertion;

class Post
{

    /**
     * @var string
     */
    private $title;

    /**
     * @var \DateTimeImmutable
     */
    private $creationDateTime;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $slug;

    /** @var PostId */
    private $id;

    /** @var  boolean */
    private $published;

    /**
     * @param PostId $id
     * @param $title
     * @param \DateTimeImmutable $creationDateTime
     */
    public function __construct(PostId $id, $title, \DateTimeImmutable $creationDateTime)
    {
        $this->id = $id;
        $this->setTitle($title);
        $this->creationDateTime = $creationDateTime;
        $this->setPublished(false);
    }

    public function title()
    {
        return $this->title;
    }

    /**
     * @param $title
     * @return mixed
     */
    private function setTitle($title)
    {
        Assertion::string($title, 'Title should be a string');
        Assertion::notBlank(trim($title), 'Title cannot be blank');
        $this->title = $title;
    }

    public function creationDateTime()
    {
        return $this->creationDateTime;
    }

    public function changeContent($content)
    {
        $this->setContent($content);
    }

    public function content()
    {
        return $this->content;
    }

    /**
     * @param $content
     */
    private function setContent($content)
    {
        Assertion::string($content, 'Content should be null or a string');
        $this->content = $content;
    }

    public function changeSlug($slug)
    {
        $this->setSlug($slug);
    }

    public function slug()
    {
        return $this->slug;
    }

    /**
     * @param $slug
     */
    private function setSlug($slug)
    {
        Assertion::string($slug, 'Slug should be a string');
        Assertion::notBlank(trim($slug), 'Slug cannot be blank');
        $this->slug = $slug;
    }

    public function id()
    {
        return $this->id;
    }

    public function publish()
    {
        $this->setPublished(true);
    }

    public function published()
    {
        return $this->published;
    }

    public function unpublish()
    {
        $this->setPublished(false);
    }

    private function setPublished($status)
    {
        $this->published = $status;
    }
}
