<?php

namespace spec\Gorka\Blog\Infrastructure\Data\Message;

use Gorka\Blog\Domain\Model\DomainMessage;
use Gorka\Blog\Infrastructure\Data\Message\Envelope;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EnvelopeSpec extends ObjectBehavior
{
    function let(DomainMessage $message, \DateTimeImmutable $creationTime)
    {
        $this->beConstructedWith($message, $creationTime);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Envelope::class);
    }

    function it_should_allow_retrieving_message(DomainMessage $message)
    {
        $this->message()->shouldBe($message);
    }

    function it_should_allow_retrieving_envelope_creation_time(\DateTimeImmutable $creationTime)
    {
        $this->creationTime()->shouldBe($creationTime);
    }
}
