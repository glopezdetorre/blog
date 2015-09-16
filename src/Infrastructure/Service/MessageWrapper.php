<?php

namespace Gorka\Blog\Infrastructure\Service;

use Gorka\Blog\Domain\Model\DomainMessage;
use Gorka\Blog\Infrastructure\Data\Message\Message;
use Gorka\Blog\Infrastructure\Data\Message\Envelope;

class MessageWrapper
{
    /**
     * @var MessageTypeToClassMapper
     */
    private $mapper;
    /**
     * @var SystemClock
     */
    private $systemClock;

    public function __construct(MessageTypeToClassMapper $mapper, SystemClock $systemClock)
    {
        $this->mapper = $mapper;
        $this->systemClock = $systemClock;
    }

    /**
     * @param DomainMessage $domainMessage
     * @return Envelope
     */
    public function wrap(DomainMessage $domainMessage)
    {
        $message = new Message($domainMessage, $this->mapper->messageTypeFromClass(get_class($domainMessage)));
        return new Envelope($message, $this->systemClock->now());
    }

    /**
     * @param Envelope $envelope
     * @return Message
     */
    public function unwrap(Envelope $envelope)
    {
        return $envelope->message()->payload();
    }
}
