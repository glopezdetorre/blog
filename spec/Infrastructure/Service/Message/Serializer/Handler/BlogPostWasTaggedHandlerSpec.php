<?php

namespace spec\Gorka\Blog\Infrastructure\Service\Message\Serializer\Handler;

use Gorka\Blog\Domain\Event\Post\PostWasTagged;
use Gorka\Blog\Domain\Model\DomainMessage;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Domain\Model\Post\Tag;
use Gorka\Blog\Infrastructure\Service\Message\Serializer\Handler\BlogPostWasTaggedHandler;
use PhpSpec\ObjectBehavior;

class BlogPostWasTaggedHandlerSpec extends ObjectBehavior
{
    const POST_ID = '25769c6c-d34d-4bfe-ba98-e0ee856f3e7a';
    const TAG_NAME = 'My tag';
    const TAG_SLUG = 'my-tag';

    function it_is_initializable()
    {
        $this->shouldHaveType(BlogPostWasTaggedHandler::class);
    }

    function it_should_serialize_post_was_published_messages()
    {
        $message = new PostWasTagged(
            PostId::create(self::POST_ID),
            Tag::create(self::TAG_NAME, self::TAG_SLUG)
        );

        $serializedMessage = [
            'id' => self::POST_ID,
            'tag' => [
                'name' => self::TAG_NAME,
                'slug' => self::TAG_SLUG
            ]
        ];

        $this->serialize($message)->shouldBe($serializedMessage);
    }

    function it_should_throw_exception_when_given_wrong_message_type(DomainMessage $message)
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('serialize', [$message]);
    }

    function it_should_unserialize_post_was_published_serialized_messages()
    {
        $message = new PostWasTagged(PostId::create(self::POST_ID), Tag::create(self::TAG_NAME, self::TAG_SLUG));
        $serializedMessage = [
            'id' => self::POST_ID,
            'tag' => [
                'name' => self::TAG_NAME,
                'slug' => self::TAG_SLUG
            ]
        ];

        $this->deserialize($serializedMessage)->shouldBeLike($message);
    }

    function it_should_throw_exception_deserializing_broken_serialized_messages()
    {
        $brokenMessage = [];
        $this->shouldThrow(\LogicException::class)->during('deserialize', [$brokenMessage]);
    }
}
