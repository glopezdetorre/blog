<?php

namespace Gorka\Blog\Infrastructure\Data\Message;

use Gorka\Blog\Infrastructure\Service\Serializer\Serializer;

class Envelope
{
    /**
     * @var Message
     */
    private $message;

    /**
     * @var \DateTimeImmutable
     */
    private $creationTime;

    public function __construct(Message $message, \DateTimeImmutable $creationTime)
    {
        $this->message = $message;
        $this->creationTime = $creationTime;
    }

    public function message()
    {
        return $this->message;
    }

    public function creationTime()
    {
        return $this->creationTime;
    }

    public function serialize()
    {
        return Serializer::serialize($this);
    }

    public static function fromSerialized($serialized)
    {
        return Serializer::deserialize($serialized, self::class);
    }
}
