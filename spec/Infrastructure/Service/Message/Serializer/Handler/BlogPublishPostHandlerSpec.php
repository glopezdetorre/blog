<?php

namespace spec\Gorka\Blog\Infrastructure\Service\Message\Serializer\Handler;

use Gorka\Blog\Domain\Command\Post\PublishPost;
use Gorka\Blog\Domain\Model\DomainMessage;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Infrastructure\Service\Message\Serializer\Handler\BlogPublishPostHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/** @mixin BlogPublishPostHandler */
class BlogPublishPostHandlerSpec extends ObjectBehavior
{
    const POST_ID = '25769c6c-d34d-4bfe-ba98-e0ee856f3e7a';

    function it_is_initializable()
    {
        $this->shouldHaveType(BlogPublishPostHandler::class);
    }

    function it_should_serialize_publish_post_messages()
    {
        $message = new PublishPost(PostId::create(self::POST_ID));
        $serializedMessage = [
            'id' => self::POST_ID
        ];

        $this->serialize($message)->shouldBe($serializedMessage);
    }

    function it_should_throw_exception_when_given_wrong_message_type(DomainMessage $message)
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('serialize', [$message]);
    }

    function it_should_unserialize_publish_post_serialized_messages()
    {
        $message = new PublishPost(PostId::create(self::POST_ID));
        $serializedMessage = [
            'id' => self::POST_ID
        ];

        $this->deserialize($serializedMessage)->shouldBeLike($message);
    }

    function it_should_throw_exception_deserializing_broken_serialized_messages()
    {
        $brokenMessage = [];
        $this->shouldThrow(\LogicException::class)->during('deserialize', [$brokenMessage]);
    }
}
