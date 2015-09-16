<?php

namespace Gorka\Blog\Infrastructure\Data\Message;

use Assert\Assertion;
use Gorka\Blog\Domain\Model\DomainMessage;

class Message
{
    /**
     * @var DomainMessage
     */
    private $payload;

    /**
     * @var string
     */
    private $type;

    public function __construct(DomainMessage $payload, $type)
    {
        $this->payload = $payload;
        $this->setType($type);
    }

    public function payload()
    {
        return $this->payload;
    }

    public function type()
    {
        return $this->type;
    }

    /**
     * @param $type
     */
    public function setType($type)
    {
        Assertion::string($type);
        Assertion::notBlank(trim($type));
        $this->type = $type;
    }
}
