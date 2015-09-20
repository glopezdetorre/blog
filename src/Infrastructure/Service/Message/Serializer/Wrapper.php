<?php

namespace Gorka\Blog\Infrastructure\Service\Message\Serializer;

use Gorka\Blog\Domain\Model\DomainMessage;
use Gorka\Blog\Infrastructure\Data\Message\Envelope;
use Gorka\Blog\Infrastructure\Service\SystemClock;

class Wrapper
{
    /**
     * @var SystemClock
     */
    private $systemClock;

    public function __construct(SystemClock $systemClock)
    {
        $this->systemClock = $systemClock;
    }

    /**
     * @param DomainMessage $domainMessage
     * @return Envelope
     */
    public function wrap(DomainMessage $domainMessage)
    {
        return new Envelope($domainMessage, $this->systemClock->now());
    }

    /**
     * @param Envelope $envelope
     * @return DomainMessage
     */
    public function unwrap(Envelope $envelope)
    {
        return $envelope->message();
    }
}
