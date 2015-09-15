<?php

namespace Gorka\Blog\Infrastructure\Data\Message;

use Gorka\Blog\Domain\Model\DomainMessage;

class Envelope
{
    /**
     * @var DomainMessage
     */
    private $message;

    /**
     * @var \DateTimeImmutable
     */
    private $creationTime;

    public function __construct(DomainMessage $message, \DateTimeImmutable $creationTime)
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
}
