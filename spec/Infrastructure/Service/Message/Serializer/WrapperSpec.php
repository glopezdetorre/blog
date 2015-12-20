<?php

namespace spec\Gorka\Blog\Infrastructure\Service\Message\Serializer;

use Gorka\Blog\Domain\Event\Post\PostWasPublished;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Infrastructure\Data\Message\Envelope;
use Gorka\Blog\Infrastructure\Service\Message\Serializer\Wrapper;
use Gorka\Blog\Infrastructure\Service\SystemClock;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/** @mixin Wrapper */
class WrapperSpec extends ObjectBehavior
{
    function let(SystemClock $systemClock)
    {
        $this->beConstructedWith($systemClock);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Wrapper::class);
    }

    public function it_should_wrap_domain_messages_into_envelopes(SystemClock $systemClock){
        $testDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2015-02-21 12:34:56');
        $domainMessage = new PostWasPublished(PostId::create('25769c6c-d34d-4bfe-ba98-e0ee856f3e7a'));

        $systemClock->now()->willReturn($testDate);

        $testType = 'blog:post_published';
        $expectedEnvelope = new Envelope(
            $domainMessage,
            $testDate
        );
        $this->wrap($domainMessage, $testType)->shouldBeLike($expectedEnvelope);
    }

    public function it_should_unwrap_envelopes_into_domain_messages(){
        $testDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2015-02-21 12:34:56');
        $domainMessage = new PostWasPublished(PostId::create('25769c6c-d34d-4bfe-ba98-e0ee856f3e7a'));

        $envelope = new Envelope(
            $domainMessage,
            $testDate
        );
        $this->unwrap($envelope)->shouldBeLike($domainMessage);
    }
}
