<?php

namespace Gorka\Blog\Infrastructure\Service;

use Assert\Assertion;

class MessageTypeToClassMapper
{
    /**
     * @var array
     */
    private $map;

    public function __construct($map = null)
    {
        if (null === $map) {
            $map = [];
        }
        $this->setMap($map);
    }

    public function classFromMessageType($messageType)
    {
        if (!isset($this->map[$messageType])) {
            throw new \LogicException(sprintf("Message type '%s' not found", $messageType));
        }
        return $this->map[$messageType];
    }

    public function messageTypeFromClass($class)
    {
        $messageType = array_search($class, $this->map, true);
        if (!$messageType) {
            throw new \LogicException(sprintf("Message class '%s' not found", $class));
        }
        return $messageType;
    }

    /**
     * @param $map
     */
    public function setMap($map)
    {
        Assertion::isArray($map);
        foreach ($map as $messageType => $class) {
            $this->guardMapping($messageType, $class);
        }
        $this->map = $map;
    }

    public function add($messageType, $class)
    {
        $this->guardMapping($messageType, $class);
        $this->map[$messageType] = $class;
    }

    /**
     * @param $messageType
     * @param $class
     */
    public function guardMapping($messageType, $class)
    {
        Assertion::string($messageType);
        Assertion::notBlank(trim($messageType));
        Assertion::string($class);
        Assertion::classExists($class);
    }
}
