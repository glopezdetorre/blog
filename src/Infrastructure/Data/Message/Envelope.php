<?php

namespace Gorka\Blog\Infrastructure\Data\Message;

use Gorka\Blog\Domain\Model\DomainMessage;

class Envelope
{

    /**
     * @var \DateTimeImmutable
     */
    private $creationTime;

    /**
     * @var DomainMessage
     */
    private $message;

    /**
     * @param DomainMessage $message
     * @param \DateTimeImmutable $creationTime
     */
    public function __construct(DomainMessage $message, \DateTimeImmutable $creationTime)
    {
        $this->message = $message;
        $this->creationTime = $creationTime;
    }

    /**
     * @return DomainMessage
     */
    public function message()
    {
        return $this->message;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function creationTime()
    {
        return $this->creationTime;
    }
}
