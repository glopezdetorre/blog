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
     * @param string $name
     */
    private function __construct($name)
    {
        $this->setName($name);
    }

    /**
     * @param string $name
     * @return static
     */
    public static function create($name)
    {
        return new static($name);
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
        Assertion::string($name);
        Assertion::notBlank(trim($name));
        $this->name = $name;
    }
}
