<?php

namespace Gorka\Blog\Domain\Model\Post;

use Assert\Assertion;

class Tag
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $slug;

    /**
     * @param string $name
     * @param string $slug
     */
    private function __construct($name, $slug)
    {
        $this->setName($name);
        $this->setSlug($slug);
    }

    /**
     * @param string $name
     * @param string $slug
     * @return static
     */
    public static function create($name, $slug)
    {
        return new static($name, $slug);
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @param $name
     */
    private function setName($name)
    {
        Assertion::string($name, 'Tag name should be a string');
        Assertion::notBlank(trim($name), 'Tag name cannot be blank');
        $this->name = $name;
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
        Assertion::false(preg_replace('/[a-z0-9]/i', '', $slug) == $slug, 'Slug should have content');
        $this->slug = $slug;
    }
}
