<?php

namespace spec\Gorka\Blog\Domain\Event\Post;

use Gorka\Blog\Domain\Event\Post\PostWasPublished;
use Gorka\Blog\Domain\Model\Post\PostId;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/** @mixin PostWasPublished */
class PostWasPublishedSpec extends ObjectBehavior
{
    function let(PostId $postId)
    {
        $this->beConstructedWith($postId);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PostWasPublished::class);
    }

    function it_should_allow_getting_post_id(PostId $postId)
    {
        $this->aggregateId()->shouldBeLike($postId);
        $this->postId()->shouldBeLike($postId);
    }

    function it_should_allow_retrieving_message_name()
    {
        $this->messageName()->shouldBe('blog:post_was_published');
    }
}
