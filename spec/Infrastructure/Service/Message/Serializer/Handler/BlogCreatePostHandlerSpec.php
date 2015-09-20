<?php

namespace spec\Gorka\Blog\Infrastructure\Service\Message\Serializer\Handler;

use Gorka\Blog\Domain\Command\Post\CreatePost;
use Gorka\Blog\Domain\Model\DomainMessage;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Infrastructure\Service\Message\Serializer\Handler\BlogCreatePostHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BlogCreatePostHandlerSpec extends ObjectBehavior
{
    const POST_ID = '25769c6c-d34d-4bfe-ba98-e0ee856f3e7a';
    const POST_TITLE = 'test title';
    const POST_CONTENT = 'test content';

    function it_is_initializable()
    {
        $this->shouldHaveType(BlogCreatePostHandler::class);
    }

    function it_should_serialize_create_post_messages()
    {
        $message = new CreatePost(PostId::create(self::POST_ID), self::POST_TITLE, self::POST_CONTENT);
        $serializedMessage = [
            'id' => self::POST_ID,
            'title' => self::POST_TITLE,
            'content' => self::POST_CONTENT
        ];

        $this->serialize($message)->shouldBe($serializedMessage);
    }

    function it_should_throw_exception_when_given_wrong_message_type(DomainMessage $message)
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('serialize', [$message]);
    }

    function it_should_unserialize_create_post_serialized_messages()
    {
        $message = new CreatePost(PostId::create(self::POST_ID), self::POST_TITLE, self::POST_CONTENT);
        $serializedMessage = [
            'id' => self::POST_ID,
            'title' => self::POST_TITLE,
            'content' => self::POST_CONTENT
        ];

        $this->deserialize($serializedMessage)->shouldBeLike($message);
    }

    function it_should_throw_exception_deserializing_broken_serialized_messages()
    {
        $brokenMessage = [];
        $this->shouldThrow(\LogicException::class)->during('deserialize', [$brokenMessage]);
    }
}
