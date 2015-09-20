<?php

namespace spec\Gorka\Blog\Domain\Event\Post;

use Gorka\Blog\Domain\Event\Post\PostWasPublished;
use Gorka\Blog\Domain\Model\Post\PostId;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PostWasPublishedSpec extends ObjectBehavior
{
    const POST_ID = '25769c6c-d34d-4bfe-ba98-e0ee856f3e7a';

    function let()
    {
        $this->beConstructedWith(PostId::create(self::POST_ID));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PostWasPublished::class);
    }

    function it_should_allow_getting_post_id()
    {
        $this->aggregateId()->shouldBeLike(self::POST_ID);
        $this->postId()->shouldBeLike(self::POST_ID);
    }

    function it_should_allow_retrieving_message_name()
    {
        $this->messageName()->shouldBe('blog:post_was_published');
    }
}
