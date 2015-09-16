<?php

namespace spec\Gorka\Blog\Infrastructure\Data\Message;

use Gorka\Blog\Domain\Event\Post\PostWasCreated;
use Gorka\Blog\Domain\Model\DomainMessage;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Infrastructure\Data\Message\Envelope;
use Gorka\Blog\Infrastructure\Data\Message\Message;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EnvelopeSpec extends ObjectBehavior
{
    function let(Message $message, \DateTimeImmutable $creationTime)
    {
        $this->beConstructedWith($message, $creationTime);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Envelope::class);
    }

    function it_should_allow_retrieving_message(Message $message)
    {
        $this->message()->shouldBe($message);
    }

    function it_should_allow_retrieving_envelope_creation_time(\DateTimeImmutable $creationTime)
    {
        $this->creationTime()->shouldBe($creationTime);
    }

    function it_should_be_serializable_to_json()
    {
        $domainMessage = new PostWasCreated(
            PostId::create('a54a1776-d347-4e75-8e8a-b6ebf034b912'),
            'Test Title',
            'Test Content'
        );
        $testType = 'post_created';
        $creationTime = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2015-02-21 21:45:02');

        $message = new Message($domainMessage, $testType);

        $this->beConstructedWith($message, $creationTime);
        $this
            ->serialize()
            ->shouldBe('{"message":{"payload":{"post_id":{"id":"a54a1776-d347-4e75-8e8a-b6ebf034b912"},"post_title":"Test Title","post_content":"Test Content"},"type":"post_created"},"creation_time":"2015-02-21T21:45:02+0100"}');
    }
}
