<?php

namespace Gorka\Blog\Domain\Model\Post;

use Assert\Assertion;
use Gorka\Blog\Domain\Exception\Post\InvalidPostIdException;
use Gorka\Blog\Domain\Model\AggregateId;

class PostId implements AggregateId
{
    /**
     * @var string
     */
    private $id;

    /**
     * @param string $id
     */
    private function __construct($id)
    {
        $this->setId($id);
    }

    /**
     * @param string $id
     * @return PostId
     */
    public static function create($id)
    {
        return new self($id);
    }

    /**
     * @return string
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return ((string) $this->id);
    }

    /**
     * @param $id
     * @throws InvalidPostIdException
     */
    private function setId($id)
    {
        try {
            Assertion::uuid($id);
        } catch (\Exception $e) {
            throw new InvalidPostIdException();
        }
        $this->id = $id;
    }
}
