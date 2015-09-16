<?php

namespace spec\Gorka\Blog\Infrastructure\Service;

use Gorka\Blog\Domain\Event\Post\PostWasPublished;
use Gorka\Blog\Domain\Model\DomainMessage;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Infrastructure\Data\Message\Envelope;
use Gorka\Blog\Infrastructure\Data\Message\Message;
use Gorka\Blog\Infrastructure\Service\MessageTypeToClassMapper;
use Gorka\Blog\Infrastructure\Service\MessageWrapper;
use Gorka\Blog\Infrastructure\Service\SystemClock;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MessageWrapperSpec extends ObjectBehavior
{
    function let(MessageTypeToClassMapper $mapper, SystemClock $systemClock)
    {
        $this->beConstructedWith($mapper, $systemClock);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MessageWrapper::class);
    }

    public function it_should_wrap_domain_messages_into_envelopes(
        MessageTypeToClassMapper $mapper,
        SystemClock $systemClock
    ){
        $testType = 'test_type';
        $testDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2015-02-21 12:34:56');
        $domainMessage = new PostWasPublished(PostId::create('25769c6c-d34d-4bfe-ba98-e0ee856f3e7a'));

        $mapper->messageTypeFromClass(get_class($domainMessage))->willReturn($testType);
        $systemClock->now()->willReturn($testDate);

        $expectedEnvelope = new Envelope(
            new Message($domainMessage, 'test_type'),
            $testDate
        );
        $this->wrap($domainMessage)->shouldBeLike($expectedEnvelope);
    }

    public function it_should_unwrap_envelopes_into_domain_messages(
        MessageTypeToClassMapper $mapper
    ){
        $testType = 'test_type';
        $testDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2015-02-21 12:34:56');
        $domainMessage = new PostWasPublished(PostId::create('25769c6c-d34d-4bfe-ba98-e0ee856f3e7a'));

        $mapper->classFromMessageType($testType)->willReturn(get_class($domainMessage));

        $envelope = new Envelope(new Message($domainMessage, $testType), $testDate);
        $this->unwrap($envelope)->shouldBeLike($domainMessage);
    }
}
