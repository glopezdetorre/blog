<?php

namespace spec\Gorka\Blog\Infrastructure\Service\Message\Serializer;

use Gorka\Blog\Domain\Event\Post\PostWasPublished;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Infrastructure\Data\Message\Envelope;
use Gorka\Blog\Infrastructure\Service\Message\Serializer\Serializer;
use Gorka\Blog\Infrastructure\Service\Message\Serializer\Wrapper;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SerializerSpec extends ObjectBehavior
{
    function let(Wrapper $wrapper)
    {
        $this->beConstructedWith($wrapper);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Serializer::class);
    }

    function it_should_serialize_messages(Wrapper $wrapper)
    {
        $message = new PostWasPublished(PostId::create('25769c6c-d34d-4bfe-ba98-e0ee856f3e7a'));
        $wrapper->wrap($message)->willReturn(
            new Envelope(
                $message,
                \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2015-02-03 12:34:56', new \DateTimeZone('Europe/Madrid'))
            )
        );

        $this->serialize($message)->shouldBe('{"message":{"type":"blog:post_was_published","payload":{"id":"25769c6c-d34d-4bfe-ba98-e0ee856f3e7a"}},"creation_time":"2015-02-03T12:34:56+0100"}');
    }

    function it_should_unserialize_messages(Wrapper $wrapper)
    {
        $expectedMessage = new PostWasPublished(PostId::create('25769c6c-d34d-4bfe-ba98-e0ee856f3e7a'));
        $envelope = new Envelope(
            $expectedMessage,
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2015-02-03 12:34:56', new \DateTimeZone('Europe/Madrid'))
        );
        $wrapper->unwrap($envelope)->willReturn($expectedMessage);

        $this
            ->deserialize('{"message":{"type":"blog:post_was_published","payload":{"id":"25769c6c-d34d-4bfe-ba98-e0ee856f3e7a"}},"creation_time":"2015-02-03T12:34:56+0100"}')
            ->shouldBeLike($expectedMessage);
    }

    function it_should_throw_exception_deserializing_broken_serialized_messages()
    {
        $invalidValues = [
            null,
            '',
            [],
            ['wut', 'wat'],
            new \StdClass(),
            json_encode([], true),
            json_encode(['message'], true),
            json_encode(['message' => []], true),
            json_encode(['message' => ['id']], true),
            json_encode(['message' => ['payload']], true),
            json_encode(['message' => ['id' => 23]], true),
            json_encode(['message' => ['payload' => '{}']], true)
        ];

        foreach ($invalidValues as $invalidValue) {
            $this->shouldThrow(\InvalidArgumentException::class)->during('deserialize', [$invalidValue]);
        }
    }
}
