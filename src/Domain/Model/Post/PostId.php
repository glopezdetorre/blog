<?php

namespace Gorka\Blog\Domain\Model\Post;

use Assert\Assertion;
use Rhumsaa\Uuid\Uuid;

class PostId
{

    /** @var  Uuid */
    private $id;

    /**
     * @param $id
     */
    protected function __construct($id)
    {
        $this->setId($id);
    }

    public static function create($id)
    {
        return new static($id);
    }

    /**
     * @return Uuid
     */
    public function id()
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->id()->toString();
    }

    /**
     * @param $id
     */
    private function setId($id)
    {
        if (!($id instanceof Uuid)) {
            try {
                Assertion::string($id, 'ID should be a string');
                Assertion::notBlank(trim($id), 'ID cannot be blank');
                Assertion::true(Uuid::isValid($id), 'ID should be a valid UUID');
                $id = Uuid::fromString($id);
            } catch (\Exception $e) {
                throw new \InvalidArgumentException($e->getMessage());
            }
        }
        $this->id = $id;
    }
}
