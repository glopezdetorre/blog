<?php

namespace spec\Gorka\Blog\Domain\Event\Post;

use Gorka\Blog\Domain\Event\Post\PostWasCreated;
use Gorka\Blog\Domain\Model\Post\PostId;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PostWasCreatedSpec extends ObjectBehavior
{
    const POST_ID = '25769c6c-d34d-4bfe-ba98-e0ee856f3e7a';
    const TEST_CONTENT = 'My content';
    const TEST_TITLE = 'My title';

    function let()
    {
        $this->beConstructedWith(PostId::create(self::POST_ID), self::TEST_TITLE, self::TEST_CONTENT);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PostWasCreated::class);
    }

    function it_should_allow_getting_post_id()
    {
        $this->aggregateId()->shouldBeLike(self::POST_ID);
        $this->postId()->shouldBeLike(self::POST_ID);
    }

    function it_should_allow_getting_post_title()
    {
        $this->postTitle()->shouldBe(self::TEST_TITLE);
    }

    function it_should_allow_getting_post_content()
    {
        $this->postContent()->shouldBe(self::TEST_CONTENT);
    }

    function it_should_allow_retrieving_message_name()
    {
        $this->messageName()->shouldBe('blog:post_was_created');
    }
}
